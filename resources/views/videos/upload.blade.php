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

        <form method="POST" action="{{ route('videos.store') }}" class="space-y-6">
            @csrf

            <div>
                <x-input-label for="name" value="Name" />
                <x-text-input
                    id="name"
                    name="name"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('name')"
                    required
                    autofocus
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-video-upload
                    name="video_url"
                    path-name="video_path"
                    original-name-name="video_original_name"
                    file-size-name="video_file_size"
                    content-type-name="video_content_type"
                    :value="old('video_url')"
                    :path-value="old('video_path')"
                    :original-name-value="old('video_original_name')"
                    :file-size-value="old('video_file_size')"
                    :content-type-value="old('video_content_type')"
                    :max-upload-mb="$maxUploadMb"
                    :max-upload-bytes="$maxUploadBytes"
                />
                <x-input-error :messages="$errors->get('video_path')" class="mt-2" />
                <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
                <x-input-error :messages="$errors->get('video_original_name')" class="mt-2" />
                <x-input-error :messages="$errors->get('video_file_size')" class="mt-2" />
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">
                    Save Video
                </button>
            </div>
        </form>
    </main>
</body>

</html>
