<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoStorageService
{
    public function maxUploadMb(): int
    {
        return (int) env('VIDEO_MAX_UPLOAD_MB', 50);
    }

    public function maxUploadBytes(): int
    {
        return $this->maxUploadMb() * 1024 * 1024;
    }

    public function createTemporaryUpload(string $filename, string $contentType): array
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $filePath = 'videos/' . date('Y/m/d/') . Str::uuid() . '.' . $extension;

        $presignedUrl = $disk->temporaryUploadUrl(
            $filePath,
            now()->addMinutes(180),
            [
                'ContentType' => $contentType,
            ],
        );

        return [
            'presigned_url' => $presignedUrl,
            'file_path' => $filePath,
            'public_url' => $disk->url($filePath),
        ];
    }

    public function confirmUpload(string $filePath): array
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        if (!$disk->exists($filePath)) {
            throw new \RuntimeException('File not found in storage');
        }

        return [
            'file_size' => $disk->size($filePath),
            'url' => $disk->url($filePath),
        ];
    }
}
