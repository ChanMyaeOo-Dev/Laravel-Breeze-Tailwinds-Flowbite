<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Services\VideoStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    public function index(VideoStorageService $videoStorage)
    {
        return view('videos.upload', [
            'maxUploadMb' => $videoStorage->maxUploadMb(),
            'maxUploadBytes' => $videoStorage->maxUploadBytes(),
        ]);
    }

    public function store(Request $request, VideoStorageService $videoStorage)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'video_path' => ['required', 'string', 'max:255'],
            'video_url' => ['required', 'url', 'max:2048'],
            'video_original_name' => ['required', 'string', 'max:255'],
            'video_file_size' => ['required', 'integer', 'min:1', 'max:' . $videoStorage->maxUploadBytes()],
        ]);

        try {
            $uploadedVideo = $videoStorage->confirmUpload($validated['video_path']);

            Video::create([
                'user_id' => $request->user()?->id,
                'name' => $validated['name'],
                'original_name' => $validated['video_original_name'],
                's3_path' => $validated['video_path'],
                'file_size' => $uploadedVideo['file_size'],
                'status' => 'completed',
            ]);

            return redirect()
                ->route('videos.upload')
                ->with('success', 'Video saved successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to store video: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors([
                    'video_path' => 'The uploaded video could not be verified. Please upload it again.',
                ]);
        }
    }

    /**
     * Generate presigned URL for direct upload to Supabase Storage
     */
    public function getPresignedUrl(Request $request, VideoStorageService $videoStorage)
    {
        $validator = Validator::make($request->all(), [
            'filename' => 'required|string|max:255',
            'content_type' => 'required|string',
            'file_size' => 'required|integer|max:' . $videoStorage->maxUploadBytes(),
        ], [
            'file_size.max' => 'The file is too large. Maximum size is ' . $videoStorage->maxUploadMb() . ' MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $upload = $videoStorage->createTemporaryUpload(
                $request->filename,
                $request->content_type,
            );

            return response()->json([
                'success' => true,
                'presigned_url' => $upload['presigned_url'],
                'file_path' => $upload['file_path'],
                'public_url' => $upload['public_url'],
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
    public function confirmUpload(Request $request, VideoStorageService $videoStorage)
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
            $upload = $videoStorage->confirmUpload($request->file_path);

            return response()->json([
                'success' => true,
                'file_size' => $upload['file_size'],
                'url' => $upload['url'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to confirm upload: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e instanceof \RuntimeException ? $e->getMessage() : 'Failed to confirm upload',
            ], $e instanceof \RuntimeException ? 404 : 500);
        }
    }
}
