<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class ImageService
{
    protected ImageManager $manager;

    protected string $disk;

    protected int $maxWidth;

    protected int $quality;

    public function __construct()
    {
        $this->manager = new ImageManager(Driver::class);
        $this->disk = config('image.disk', 's3');
        $this->maxWidth = config('image.max_width', 1200);
        $this->quality = config('image.quality', 80);
    }

    /**
     * Store an uploaded file and optimize it.
     *
     * @throws \RuntimeException
     */
    public function storeOptimized(UploadedFile $file, string $directory = 'images'): string
    {
        $originalPath = $this->storeRaw($file, $directory);

        try {
            $this->optimize($originalPath);
        } catch (\Throwable $e) {
            $this->delete($originalPath);

            throw new \RuntimeException(
                "Image optimization failed: {$e->getMessage()}",
                previous: $e
            );
        }

        return $originalPath;
    }

    /**
     * Store raw uploaded file without optimization.
     *
     * @throws \RuntimeException
     */
    public function storeRaw(UploadedFile $file, string $directory = 'images'): string
    {
        try {
            $path = $file->store($directory, $this->disk);
        } catch (\Throwable $e) {
            throw new \RuntimeException(
                "Failed to store image to {$this->disk}: {$e->getMessage()}",
                previous: $e
            );
        }

        if ($path === false) {
            throw new \RuntimeException("Storage::put returned false for disk [{$this->disk}].");
        }

        return $path;
    }

    /**
     * Optimize an existing image on the configured disk.
     *
     * For remote disks (S3), the image is downloaded to a temp file,
     * optimized, then re-uploaded. Temp files are always cleaned up.
     *
     * @throws \RuntimeException
     */
    public function optimize(string $path): void
    {
        $isRemote = $this->isRemoteDisk();
        $tempPath = null;

        try {
            $contents = Storage::disk($this->disk)->get($path);

            if ($contents === null) {
                throw new \RuntimeException("Could not read image at [{$path}] from disk [{$this->disk}].");
            }

            $tempPath = storage_path('app/temp_'.Str::random(40).'.tmp');
            file_put_contents($tempPath, $contents);

            $image = $this->manager->decode($tempPath);

            if ($image->width() > $this->maxWidth) {
                $image->scaleDown(width: $this->maxWidth);
            }

            $encoded = $image->encode(new WebpEncoder(quality: $this->quality));
            Storage::disk($this->disk)->put($path, $encoded);
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new \RuntimeException(
                "Image optimization failed for [{$path}]: {$e->getMessage()}",
                previous: $e
            );
        } finally {
            if ($tempPath && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    /**
     * Delete an image from the disk.
     */
    public function delete(string $path): bool
    {
        try {
            if (Storage::disk($this->disk)->exists($path)) {
                return Storage::disk($this->disk)->delete($path);
            }
        } catch (\Throwable $e) {
            report(new \RuntimeException(
                "Failed to delete image [{$path}]: {$e->getMessage()}",
                previous: $e
            ));
        }

        return false;
    }

    /**
     * Get the URL for an image path.
     */
    public function getUrl(string $path): ?string
    {
        try {
            return Storage::url($path);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getDisk(): string
    {
        return $this->disk;
    }

    public function setDisk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function setMaxWidth(int $maxWidth): static
    {
        $this->maxWidth = $maxWidth;

        return $this;
    }

    public function setQuality(int $quality): static
    {
        $this->quality = max(1, min(100, $quality));

        return $this;
    }

    protected function isRemoteDisk(): bool
    {
        return in_array($this->disk, ['s3']);
    }
}
