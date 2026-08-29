<?php

namespace App\Console\Commands;

use App\Models\Feedback;
use Illuminate\Console\Command;
use Rubix\ML\Classifiers\MultilayerPerceptron;
use Rubix\ML\CrossValidation\Metrics\Accuracy;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\NeuralNet\ActivationFunctions\ReLU;
use Rubix\ML\NeuralNet\Layers\Activation;
use Rubix\ML\NeuralNet\Layers\Dense;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Pipeline;
use Rubix\ML\Tokenizers\NGram;
use Rubix\ML\Transformers\StopWordFilter;
use Rubix\ML\Transformers\TextNormalizer;
use Rubix\ML\Transformers\TfIdfTransformer;
use Rubix\ML\Transformers\WordCountVectorizer;

class TrainSentimentModel extends Command
{
    /**
     * Run:
     *
     * php artisan ml:train-sentiment
     *
     * Example:
     *
     * php artisan ml:train-sentiment --test-size=0.2
     */
    protected $signature = 'ml:train-sentiment {--test-size=0.2}';

    protected $description = 'Train sentiment analysis model from customer feedback';

    public function handle(): int
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Get feedback data
        |--------------------------------------------------------------------------
        */

        $feedbacks = Feedback::query()
            ->whereNotNull('comment')
            ->whereNotNull('rating')
            ->where('comment', '!=', '')
            ->get();

        if ($feedbacks->count() < 100) {
            $this->error(
                'Need at least 100 feedbacks. '.
                "Currently found {$feedbacks->count()}."
            );

            return Command::FAILURE;
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Convert ratings into sentiment labels
        |--------------------------------------------------------------------------
        */

        $samples = [];
        $labels = [];

        foreach ($feedbacks as $feedback) {
            $samples[] = trim($feedback->comment);

            $labels[] = $this->ratingToSentiment(
                (int) $feedback->rating
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Show dataset information
        |--------------------------------------------------------------------------
        */

        $positive = count(
            array_filter(
                $labels,
                fn ($label) => $label === 'positive'
            )
        );

        $neutral = count(
            array_filter(
                $labels,
                fn ($label) => $label === 'neutral'
            )
        );

        $negative = count(
            array_filter(
                $labels,
                fn ($label) => $label === 'negative'
            )
        );

        $this->info(
            'Total feedbacks: '.count($samples)
        );

        $this->info(
            "Positive: {$positive}"
        );

        $this->info(
            "Neutral: {$neutral}"
        );

        $this->info(
            "Negative: {$negative}"
        );

        /*
        |--------------------------------------------------------------------------
        | 4. Create labeled dataset
        |--------------------------------------------------------------------------
        */

        $dataset = new Labeled(
            $samples,
            $labels
        );

        /*
        |--------------------------------------------------------------------------
        | 5. Build ML pipeline
        |--------------------------------------------------------------------------
        |
        | TextNormalizer
        |       ↓
        | StopWordFilter
        |       ↓
        | WordCountVectorizer
        |       ↓
        | TF-IDF
        |       ↓
        | Multinomial Naive Bayes
        |
        */

        $pipeline = new Pipeline(
            [
                new TextNormalizer,

                new StopWordFilter([
                    'english',
                ]),

                new WordCountVectorizer(
                    10000,
                    1,
                    0.8,
                    new NGram(1, 2)
                ),

                new TfIdfTransformer,
            ],

            new MultilayerPerceptron([
                new Dense(64),
                new Activation(new ReLU),
                new Dense(32),
                new Activation(new ReLU),
            ])
        );

        /*
        |--------------------------------------------------------------------------
        | 6. Split training/testing data
        |--------------------------------------------------------------------------
        */

        $testSize = (float) $this->option('test-size');

        if ($testSize <= 0 || $testSize >= 1) {
            $this->error(
                'The test size must be between 0 and 1.'
            );

            return Command::FAILURE;
        }

        $trainingSize = 1 - $testSize;

        [$training, $testing] = $dataset->stratifiedSplit(
            $trainingSize
        );

        $this->info(
            "Training samples: {$training->numSamples()}"
        );

        $this->info(
            "Testing samples: {$testing->numSamples()}"
        );

        /*
        |--------------------------------------------------------------------------
        | 7. Train model
        |--------------------------------------------------------------------------
        */

        $this->info('Training sentiment model...');

        $pipeline->train($training);

        $this->info('Training completed.');

        /*
        |--------------------------------------------------------------------------
        | 8. Test model
        |--------------------------------------------------------------------------
        */

        $this->info('Evaluating model...');

        $predictions = $pipeline->predict(
            $testing
        );

        $metric = new Accuracy;

        $accuracy = $metric->score(
            $predictions,
            $testing->labels()
        );

        $accuracyPercentage = round(
            $accuracy * 100,
            2
        );

        $this->info(
            "Validation accuracy: {$accuracyPercentage}%"
        );

        /*
        |--------------------------------------------------------------------------
        | 9. Save model
        |--------------------------------------------------------------------------
        */

        $modelDirectory = storage_path('ml');

        if (! is_dir($modelDirectory)) {
            mkdir(
                $modelDirectory,
                0755,
                true
            );
        }

        $modelPath = $modelDirectory.
            DIRECTORY_SEPARATOR.
            'sentiment_model.rbx';

        /*
        | PersistentModel allows the entire pipeline
        | and trained classifier to be saved.
        */
        $persistentModel = new PersistentModel(
            $pipeline,
            new Filesystem($modelPath)
        );

        $persistentModel->save();

        /*
        |--------------------------------------------------------------------------
        | 10. Finished
        |--------------------------------------------------------------------------
        */

        $this->newLine();

        $this->info(
            'Sentiment model trained successfully!'
        );

        $this->info(
            "Model: {$modelPath}"
        );

        $this->info(
            "Accuracy: {$accuracyPercentage}%"
        );

        return Command::SUCCESS;
    }

    /**
     * Convert restaurant rating into sentiment.
     */
    private function ratingToSentiment(int $rating): string
    {
        return match (true) {
            $rating >= 4 => 'positive',
            $rating === 3 => 'neutral',
            default => 'negative',
        };
    }
}
