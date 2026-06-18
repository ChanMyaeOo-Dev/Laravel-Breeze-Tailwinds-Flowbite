<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Services\VideoStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            'video' => [
                'required',
                'file',
                'mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo,video/mov',
                'max:' . $videoStorage->maxUploadKilobytes(),
            ],
        ]);

        try {
            $uploadedVideo = $videoStorage->upload($validated['video']);

            Video::create([
                'user_id' => $request->user()?->id,
                'name' => $validated['name'],
                'original_name' => $uploadedVideo['original_name'],
                's3_path' => $uploadedVideo['s3_path'],
                'file_size' => $uploadedVideo['file_size'],
                'status' => 'completed',
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Video saved successfully.',
                    'redirect' => route('videos.upload'),
                ]);
            }

            return redirect()
                ->route('videos.upload')
                ->with('success', 'Video saved successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to store video: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The video could not be uploaded. Please try again.',
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors([
                    'video' => 'The video could not be uploaded. Please try again.',
                ]);
        }
    }
}
