<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    public function index()
    {
        $maxUploadMb = (int) env('VIDEO_MAX_UPLOAD_MB', 50);
        return view('videos.upload', [
            'maxUploadMb' => $maxUploadMb,
            'maxUploadBytes' => $maxUploadMb * 1024 * 1024,
        ]);
    }

    /**
     * Generate presigned URL for direct upload to Supabase Storage
     */
    public function getPresignedUrl(Request $request)
    {
        $maxUploadMb = (int) env('VIDEO_MAX_UPLOAD_MB', 50);
        $maxUploadBytes = $maxUploadMb * 1024 * 1024;

        $validator = Validator::make($request->all(), [
            'filename' => 'required|string|max:255',
            'content_type' => 'required|string',
            'file_size' => 'required|integer|max:' . $maxUploadBytes,
        ], [
            'file_size.max' => 'The file is too large. Maximum size is ' . $maxUploadMb . ' MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('s3');

            // Generate unique file path
            $extension = pathinfo($request->filename, PATHINFO_EXTENSION);
            $filePath = 'videos/' . date('Y/m/d/') . Str::uuid() . '.' . $extension;

            // Generate presigned URL valid for 60 minutes
            $presignedUrl = $disk->temporaryUploadUrl(
                $filePath,
                now()->addMinutes(180),
                [
                    'ContentType' => $request->content_type,
                ]
            );

            // Get the public URL
            $publicUrl = $disk->url($filePath);

            return response()->json([
                'success' => true,
                'presigned_url' => $presignedUrl,
                'file_path' => $filePath,
                'public_url' => $publicUrl,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate presigned URL: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate upload URL. Please try again.'
            ], 500);
        }
    }

    /**
     * Confirm successful upload
     */
    public function confirmUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('s3');

            // Verify file exists
            if (!$disk->exists($request->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found in storage'
                ], 404);
            }

            // Get file details
            $fileSize = $disk->size($request->file_path);
            $url = $disk->url($request->file_path);

            return response()->json([
                'success' => true,
                'file_size' => $fileSize,
                'url' => $url,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to confirm upload: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm upload'
            ], 500);
        }
    }
}
