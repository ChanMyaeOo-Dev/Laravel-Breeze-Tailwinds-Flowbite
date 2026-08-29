<?php

namespace App\Services\AI;

use App\Models\Feedback;
use App\Models\FeedbackAnalysis;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;

class MlSentimentAnalysisService
{
    protected PersistentModel $model;

    protected string $modelPath;

    protected string $version = 'mlp_v1';

    public function __construct()
    {
        $this->modelPath = storage_path(
            'ml/sentiment_model.rbx'
        );

        $this->loadModel();
    }

    /**
     * Load the trained Rubix ML model.
     */
    protected function loadModel(): void
    {
        if (! file_exists($this->modelPath)) {
            throw new \RuntimeException(
                'ML model not found. '.
                'Run: php artisan ml:train-sentiment'
            );
        }

        try {
            $this->model = PersistentModel::load(
                new Filesystem($this->modelPath)
            );
        } catch (\Throwable $e) {
            throw new \RuntimeException(
                'Unable to load the sentiment model. '.
                'Delete the model and retrain it: '.
                'rm storage/ml/sentiment_model.rbx && '.
                'php artisan ml:train-sentiment',
                0,
                $e
            );
        }
    }

    /**
     * Analyze one feedback and store the result.
     */
    public function analyze(Feedback $feedback): FeedbackAnalysis
    {
        /*
        |--------------------------------------------------------------------------
        | Get feedback text
        |--------------------------------------------------------------------------
        */

        $text = trim((string) $feedback->comment);

        if ($text === '') {
            throw new \InvalidArgumentException(
                'Feedback comment cannot be empty.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create dataset
        |--------------------------------------------------------------------------
        */

        $dataset = new Unlabeled([
            $text,
        ]);

        $datasetForProba = new Unlabeled([
            $text,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Predict sentiment
        |--------------------------------------------------------------------------
        */

        $predictions = $this->model->predict(
            $dataset
        );

        $prediction = $predictions[0];

        /*
        |--------------------------------------------------------------------------
        | Get probabilities
        |--------------------------------------------------------------------------
        */

        $probabilities = $this->model->proba(
            $datasetForProba
        );

        $probabilities = $probabilities[0];

        /*
        |--------------------------------------------------------------------------
        | Confidence
        |--------------------------------------------------------------------------
        */

        $confidence = max($probabilities);

        /*
        |--------------------------------------------------------------------------
        | Additional analysis
        |--------------------------------------------------------------------------
        */

        $categories = $this->detectCategories(
            $text
        );

        $keywords = $this->extractKeywords(
            $text
        );

        $summary = $this->summarize(
            $text
        );

        /*
        |--------------------------------------------------------------------------
        | Save analysis
        |--------------------------------------------------------------------------
        */

        return FeedbackAnalysis::updateOrCreate(
            [
                'feedback_id' => $feedback->id,
            ],
            [
                'sentiment' => $prediction,

                'confidence' => $confidence,

                'probabilities' => $probabilities,

                'categories' => $categories,

                'keywords' => $keywords,

                'summary' => $summary,

                'model_version' => $this->version,
            ]
        );
    }

    /**
     * Detect common restaurant feedback categories.
     */
    protected function detectCategories(string $text): array
    {
        $text = strtolower($text);

        $categories = [];

        $categoryKeywords = [
            'food' => [
                'food',
                'meal',
                'dish',
                'taste',
                'flavor',
                'delicious',
                'bland',
                'menu',
            ],

            'service' => [
                'service',
                'waiter',
                'waitress',
                'staff',
                'employee',
                'server',
                'friendly',
                'rude',
            ],

            'cleanliness' => [
                'clean',
                'dirty',
                'cleanliness',
                'hygiene',
                'toilet',
                'bathroom',
                'table',
            ],

            'price' => [
                'price',
                'expensive',
                'cheap',
                'cost',
                'value',
                'worth',
            ],

            'waiting_time' => [
                'wait',
                'waiting',
                'slow',
                'fast',
                'quick',
                'delay',
                'long time',
            ],

            'atmosphere' => [
                'atmosphere',
                'environment',
                'music',
                'quiet',
                'loud',
                'comfortable',
                'ambience',
            ],
        ];

        foreach ($categoryKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    $categories[] = $category;

                    break;
                }
            }
        }

        return array_values(
            array_unique($categories)
        );
    }

    /**
     * Extract simple keywords from feedback.
     */
    protected function extractKeywords(string $text): array
    {
        $text = strtolower($text);

        /*
        |--------------------------------------------------------------------------
        | Remove punctuation
        |--------------------------------------------------------------------------
        */

        $text = preg_replace(
            '/[^\p{L}\p{N}\s]/u',
            '',
            $text
        );

        /*
        |--------------------------------------------------------------------------
        | Split words
        |--------------------------------------------------------------------------
        */

        $words = preg_split(
            '/\s+/',
            $text,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        /*
        |--------------------------------------------------------------------------
        | Basic stop words
        |--------------------------------------------------------------------------
        */

        $stopWords = [
            'the',
            'a',
            'an',
            'and',
            'or',
            'but',
            'is',
            'was',
            'are',
            'were',
            'to',
            'of',
            'in',
            'on',
            'for',
            'with',
            'this',
            'that',
            'it',
            'very',
            'i',
            'we',
            'they',
            'you',
        ];

        /*
        |--------------------------------------------------------------------------
        | Filter keywords
        |--------------------------------------------------------------------------
        */

        $keywords = array_filter(
            $words,
            function ($word) use ($stopWords) {
                return strlen($word) >= 3
                    && ! in_array(
                        $word,
                        $stopWords,
                        true
                    );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Count occurrences
        |--------------------------------------------------------------------------
        */

        $frequency = array_count_values(
            $keywords
        );

        /*
        |--------------------------------------------------------------------------
        | Sort most common first
        |--------------------------------------------------------------------------
        */

        arsort($frequency);

        /*
        |--------------------------------------------------------------------------
        | Return top 10
        |--------------------------------------------------------------------------
        */

        return array_slice(
            array_keys($frequency),
            0,
            10
        );
    }

    /**
     * Generate a simple summary.
     *
     * This is rule-based, not an LLM summary.
     */
    protected function summarize(string $text): string
    {
        $text = trim($text);

        if (strlen($text) <= 150) {
            return $text;
        }

        return substr($text, 0, 147).'...';
    }
}
