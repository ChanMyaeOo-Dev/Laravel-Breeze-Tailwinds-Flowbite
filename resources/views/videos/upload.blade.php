<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Video Upload</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-100 px-4 py-10 font-sans text-gray-900">
    <main class="mx-auto max-w-2xl rounded bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-gray-900">Create Video</h1>
            <p class="mt-1 text-sm text-gray-500">Add a name, upload the video, then save it to your videos table.</p>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <x-input-label for="name" value="Name" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')"
                    required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-video-upload name="video" :max-upload-mb="$maxUploadMb" :max-upload-bytes="$maxUploadBytes" required />
                <x-input-error :messages="$errors->get('video')" class="mt-2" />
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">
                    Save Video
                </button>
            </div>
        </form>

        <div style="display: flex; flex-wrap: wrap; margin: 20px 0;">
            @forelse ($videos as $video)
                <a href="{{ Storage::disk('s3')->url($video->s3_path) }}" class="" target="_blank"
                    rel="noopener noreferrer" style="border: .5px solid #00000030; padding: 5px 10px; margin: 5px;">
                    {{ $video->name }}
                </a>
            @empty
                <p>There is no video uploaded...</p>
            @endforelse
        </div>

    </main>
</body>

</html>
