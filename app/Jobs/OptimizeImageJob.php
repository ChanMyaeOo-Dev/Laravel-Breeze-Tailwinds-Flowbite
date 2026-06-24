<?php

namespace App\Jobs;

use App\Services\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OptimizeImageJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public int $uniqueFor = 3600;

    public function __construct(
        public string $path,
        public string $disk,
    ) {}

    public function uniqueId(): string
    {
        return $this->disk.':'.$this->path;
    }

    public function handle(ImageService $imageService): void
    {
        $imageService->setDisk($this->disk);

        $imageService->optimize($this->path);

        Log::info('Image optimized', [
            'path' => $this->path,
            'disk' => $this->disk,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Image optimization failed', [
            'path' => $this->path,
            'disk' => $this->disk,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}
