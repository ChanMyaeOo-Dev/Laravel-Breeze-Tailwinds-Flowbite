<?php

namespace App\Services\AI;

use Illuminate\Support\Collection;

class FeedbackSummaryService
{
    public function generate(Collection $feedbacks): string
    {
        $analyzed = $feedbacks->filter->analysis;

        if ($analyzed->isEmpty()) {
            return $this->noDataSummary($feedbacks->count());
        }

        $total = $feedbacks->count();
        $avgRating = round($feedbacks->avg('rating'), 1);

        $sentimentCounts = $analyzed->groupBy('analysis.sentiment');

        $positiveCount = ($sentimentCounts->get('positive')?->count()) ?? 0;
        $neutralCount = ($sentimentCounts->get('neutral')?->count()) ?? 0;
        $negativeCount = ($sentimentCounts->get('negative')?->count()) ?? 0;

        $analyzedCount = $analyzed->count();

        $positivePct = round(($positiveCount / $analyzedCount) * 100);
        $neutralPct = round(($neutralCount / $analyzedCount) * 100);
        $negativePct = round(($negativeCount / $analyzedCount) * 100);

        $categorySentiment = $this->getCategorySentiment($analyzed);
        $negativeKeywords = $this->getNegativeKeywords($analyzed);

        $paragraph1 = $this->buildOverallAssessment(
            $total,
            $avgRating,
            $positivePct,
            $neutralPct,
            $negativePct,
            $categorySentiment
        );

        $paragraph2 = $this->buildAdvice(
            $negativePct,
            $categorySentiment,
            $negativeKeywords
        );

        return $paragraph1."\n\n".$paragraph2;
    }

    private function noDataSummary(int $total): string
    {
        if ($total === 0) {
            return 'No customer feedback has been collected yet. '
                .'Once customers start submitting feedback, AI analysis '
                .'will generate insights and recommendations for your restaurant.';
        }

        return "You have {$total} customer feedback(s) that have not yet been "
            .'analyzed. Run the batch analysis command to generate AI-powered '
            .'insights and personalized recommendations for your restaurant.';
    }

    private function getCategorySentiment(Collection $analyzed): array
    {
        $categoryData = [];

        foreach ($analyzed as $feedback) {
            $categories = $feedback->analysis->categories ?? [];
            $sentiment = $feedback->analysis->sentiment;

            foreach ($categories as $category) {
                if (! isset($categoryData[$category])) {
                    $categoryData[$category] = [
                        'positive' => 0,
                        'neutral' => 0,
                        'negative' => 0,
                        'total' => 0,
                    ];
                }

                $categoryData[$category][$sentiment]++;
                $categoryData[$category]['total']++;
            }
        }

        uasort($categoryData, fn ($a, $b) => $b['total'] <=> $a['total']);

        return $categoryData;
    }

    private function getNegativeKeywords(Collection $analyzed): array
    {
        $keywords = [];

        $stopKeywords = [
            'restaurant',
            'food',
            'service',
            'place',
            'meal',
            'experience',
            'time',
            'staff',
            'order',
            'would',
            'not',
            'our',
            'had',
            'was',
            'the',
            'and',
            'for',
            'are',
            'but',
            'been',
            'have',
            'this',
            'that',
            'with',
            'they',
            'you',
            'from',
            'one',
            'all',
            'were',
            'also',
            'did',
            'very',
            'could',
            'just',
            'their',
            'its',
            'at',
            'an',
            'be',
            'has',
            'how',
            'her',
            'him',
            'his',
            'she',
            'them',
            'then',
            'than',
            'too',
            'can',
            'will',
            'do',
            'did',
            'got',
            'get',
            'out',
            'about',
            'into',
            'over',
            'such',
            'that',
            'what',
            'when',
            'who',
            'which',
            'there',
            'go',
            'going',
            'came',
            'come',
            'take',
            'made',
            'make',
        ];

        foreach ($analyzed->where('analysis.sentiment', 'negative') as $feedback) {
            $feedbackKeywords = $feedback->analysis->keywords ?? [];

            foreach ($feedbackKeywords as $keyword) {
                if (in_array($keyword, $stopKeywords, true)) {
                    continue;
                }

                $keywords[$keyword] = ($keywords[$keyword] ?? 0) + 1;
            }
        }

        arsort($keywords);

        return array_slice($keywords, 0, 5, true);
    }

    private function buildOverallAssessment(
        int $total,
        float $avgRating,
        int $positivePct,
        int $neutralPct,
        int $negativePct,
        array $categorySentiment
    ): string {
        $text = "Based on analysis of {$total} customer feedbacks, "
            ."your restaurant holds an average rating of {$avgRating}/5 "
            ."with sentiment distribution of {$positivePct}% positive, "
            ."{$neutralPct}% neutral, and {$negativePct}% negative.";

        $sentimentDesc = match (true) {
            $positivePct >= 60 => 'predominantly positive',
            $positivePct >= 40 => 'mixed, with a notable balance of positive and negative opinions',
            default => 'leaning toward negative',
        };

        $text .= " Overall, customer sentiment is {$sentimentDesc}.";

        $praisedCategories = $this->getTopCategoriesBySentiment(
            $categorySentiment,
            'positive'
        );

        if (count($praisedCategories) >= 2) {
            $items = implode(' and ', array_slice($praisedCategories, 0, 2));
            $text .= " Customers particularly appreciate your {$items}";
        } elseif (count($praisedCategories) === 1) {
            $text .= " Your {$praisedCategories[0]} consistently receives praise";
        }

        if (! empty($praisedCategories)) {
            $text .= ', highlighting these as key strengths of your restaurant.';
        } else {
            $text .= ' While no single area stands out as a clear strength, '
                .'there is an opportunity to build on your positive feedback.';
        }

        return $text;
    }

    private function buildAdvice(
        int $negativePct,
        array $categorySentiment,
        array $negativeKeywords
    ): string {
        if ($negativePct <= 15) {
            $text = 'Your restaurant is performing well with minimal negative feedback. ';
            $text .= 'To maintain this standard, continue focusing on consistency '
                .'in food quality and service. ';
            $text .= 'Consider using the positive feedback as testimonials '
                .'and keep monitoring customer sentiment to catch any issues early.';

            return $text;
        }

        $problemCategories = $this->getTopCategoriesBySentiment(
            $categorySentiment,
            'negative'
        );

        $text = "However, {$negativePct}% of feedbacks express concern";

        if (count($problemCategories) >= 2) {
            $items = implode(' and ', array_slice($problemCategories, 0, 2));
            $text .= ", particularly regarding {$items}";
        } elseif (count($problemCategories) === 1) {
            $text .= ", especially about {$problemCategories[0]}";
        }

        $text .= '. ';

        if (! empty($negativeKeywords)) {
            $topIssues = implode(', ', array_slice(array_keys($negativeKeywords), 0, 3));
            $text .= "Common complaint keywords include \"{$topIssues}\". ";
        }

        $text .= 'We recommend prioritizing improvements in these areas '
            .'to reduce negative experiences and boost overall customer satisfaction.';

        return $text;
    }

    private function getTopCategoriesBySentiment(
        array $categorySentiment,
        string $sentiment
    ): array {
        $ranked = [];

        foreach ($categorySentiment as $category => $counts) {
            $total = $counts['total'];

            if ($total < 3) {
                continue;
            }

            $ratio = $counts[$sentiment] / $total;

            if ($sentiment === 'negative' && $ratio < 0.25) {
                continue;
            }

            $ranked[$category] = $ratio * $total;
        }

        arsort($ranked);

        return array_map(
            fn ($cat) => str_replace('_', ' ', $cat),
            array_keys($ranked)
        );
    }
}
