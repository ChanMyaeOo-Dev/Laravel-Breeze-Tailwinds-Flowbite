<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::get('/videos/upload', [VideoController::class, 'index'])->name('videos.upload');
Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');

Route::get('/test-s3', function () {
    $results = [];

    // Show current config
    $results['config'] = [
        'bucket' => config('filesystems.disks.s3.bucket'),
        'region' => config('filesystems.disks.s3.region'),
        'endpoint' => config('filesystems.disks.s3.endpoint'),
        'key_exists' => !empty(config('filesystems.disks.s3.key')),
        'secret_exists' => !empty(config('filesystems.disks.s3.secret')),
        'use_path_style' => config('filesystems.disks.s3.use_path_style_endpoint'),
    ];

    try {
        // Use correct endpoint for Supabase
        $s3Client = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => 'ap-southeast-1',
            'endpoint' => 'https://fzdajbpioimqagnmpcih.storage.supabase.co/storage/v1/s3',
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        // Test listing objects
        $objects = $s3Client->listObjects([
            'Bucket' => 'next-shop',
            'MaxKeys' => 5,
        ]);

        $results['connection'] = 'SUCCESS';
        $results['objects_count'] = count($objects['Contents'] ?? []);

        // Test upload
        $key = 'test/supabase-test-' . time() . '.txt';
        $s3Client->putObject([
            'Bucket' => 'next-shop',
            'Key' => $key,
            'Body' => 'Supabase connection test!',
            'ContentType' => 'text/plain',
        ]);

        $results['upload_test'] = 'SUCCESS';
        $results['test_file'] = $key;

        // Test presigned URL
        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => 'next-shop',
            'Key' => 'test/presigned-' . time() . '.txt',
            'ContentType' => 'text/plain',
        ]);

        $request = $s3Client->createPresignedRequest($cmd, '+1 hour');
        $results['presigned_url'] = (string) $request->getUri();
    } catch (\Exception $e) {
        $results['error'] = $e->getMessage();
        $results['error_code'] = method_exists($e, 'getAwsErrorCode') ? $e->getAwsErrorCode() : null;
        $results['trace'] = $e->getTraceAsString();
    }

    // Test Laravel Storage
    try {
        $disk = Storage::disk('s3');
        $testPath = 'test/laravel-test-' . time() . '.txt';
        $disk->put($testPath, 'Laravel Supabase test');
        $results['laravel_storage'] = 'SUCCESS';
        $results['laravel_test_file'] = $testPath;
    } catch (\Exception $e) {
        $results['laravel_storage'] = 'FAILED';
        $results['laravel_error'] = $e->getMessage();
    }

    return response()->json($results, 200, [], JSON_PRETTY_PRINT);
});

require __DIR__ . '/admin.php';
require __DIR__ . '/auth.php';
