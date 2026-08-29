<?php

namespace App\Console\Commands;

use App\Models\Feedback;
use App\Services\AI\MlSentimentAnalysisService;
use Illuminate\Console\Command;

class AnalyzeFeedbackBatch extends Command
{
    protected $signature = 'feedback:analyze-ml {--batch-size=100}';

    protected $description = 'Analyze unprocessed feedback using ML model';

    public function handle(MlSentimentAnalysisService $service): int
    {
        $feedbacks = Feedback::whereDoesntHave('analysis')
            ->orderBy('created_at')
            ->limit($this->option('batch-size'))
            ->get();

        if ($feedbacks->isEmpty()) {
            $this->info('No unprocessed feedbacks.');

            return Command::SUCCESS;
        }

        $this->info("Processing {$feedbacks->count()} feedbacks...");
        $bar = $this->output->createProgressBar($feedbacks->count());
        $bar->start();

        foreach ($feedbacks as $fb) {
            try {
                $service->analyze($fb);
            } catch (\Exception $e) {
                $this->error("Failed on feedback {$fb->id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Batch analysis complete.');

        return Command::SUCCESS;
    }
}
