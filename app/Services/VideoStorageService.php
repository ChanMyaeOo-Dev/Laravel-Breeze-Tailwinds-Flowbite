<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
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

    public function maxUploadKilobytes(): int
    {
        return $this->maxUploadMb() * 1024;
    }

    public function upload(UploadedFile $video): array
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        $extension = $video->getClientOriginalExtension() ?: $video->extension();
        $filePath = 'videos/' . date('Y/m/d/') . Str::uuid() . ($extension ? '.' . $extension : '');
        $stream = fopen($video->getRealPath(), 'r');

        try {
            $disk->put($filePath, $stream, [
                'ContentType' => $video->getMimeType() ?: 'application/octet-stream',
            ]);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        return [
            'original_name' => $video->getClientOriginalName(),
            's3_path' => $filePath,
            'file_size' => $disk->size($filePath),
            'url' => $disk->url($filePath),
        ];
    }
}
