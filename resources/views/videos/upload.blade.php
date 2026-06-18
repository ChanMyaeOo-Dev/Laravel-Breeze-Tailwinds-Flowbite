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
        <x-video-upload name="video_url" path-name="video_path" :max-upload-mb="$maxUploadMb" :max-upload-bytes="$maxUploadBytes" />
    </main>
</body>

</html>
