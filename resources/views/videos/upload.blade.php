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

        <div id="formError" class="mb-5 hidden rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        </div>

        <form id="videoForm" method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data"
            class="space-y-6">
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
                <button id="submitButton" type="submit" class="btn-primary">
                    Save Video
                </button>
            </div>

            <div id="uploadProgress" class="hidden space-y-2">
                <div class="h-3 overflow-hidden rounded bg-gray-200">
                    <div id="uploadProgressFill" class="h-full rounded bg-success transition-all" style="width: 0%">
                    </div>
                </div>
                <p id="uploadProgressText" class="text-center text-xs text-gray-500">Preparing upload...</p>
            </div>
        </form>
    </main>

    <script>
        const videoForm = document.getElementById('videoForm');
        const submitButton = document.getElementById('submitButton');
        const uploadProgress = document.getElementById('uploadProgress');
        const uploadProgressFill = document.getElementById('uploadProgressFill');
        const uploadProgressText = document.getElementById('uploadProgressText');
        const formError = document.getElementById('formError');

        videoForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const xhr = new XMLHttpRequest();
            const formData = new FormData(videoForm);
            const uploadStartTime = Date.now();

            formError.classList.add('hidden');
            formError.textContent = '';
            uploadProgress.classList.remove('hidden');
            uploadProgressFill.style.width = '0%';
            uploadProgressText.textContent = 'Preparing upload...';
            submitButton.disabled = true;
            submitButton.textContent = 'Uploading...';

            xhr.upload.addEventListener('progress', function(progressEvent) {
                if (!progressEvent.lengthComputable) {
                    return;
                }

                const percentComplete = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                const elapsed = (Date.now() - uploadStartTime) / 1000;
                const speed = elapsed > 0 ? progressEvent.loaded / elapsed : 0;

                uploadProgressFill.style.width = percentComplete + '%';
                uploadProgressText.textContent =
                    percentComplete + '% - ' +
                    formatFileSize(progressEvent.loaded) + ' / ' +
                    formatFileSize(progressEvent.total) +
                    ' (' + formatFileSize(speed) + '/s)';
            });

            xhr.addEventListener('load', function() {
                let response = {};

                try {
                    response = JSON.parse(xhr.responseText);
                } catch (error) {}

                if (xhr.status >= 200 && xhr.status < 300 && response.success) {
                    uploadProgressFill.style.width = '100%';
                    uploadProgressText.textContent = 'Upload complete. Saving video...';
                    window.location.href = response.redirect || @js(route('videos.upload'));
                    return;
                }

                showUploadError(response.message || firstValidationError(response.errors) ||
                    'Upload failed. Please try again.');
            });

            xhr.addEventListener('error', function() {
                showUploadError('Network error. Please check your connection and try again.');
            });

            xhr.addEventListener('abort', function() {
                showUploadError('Upload cancelled.');
            });

            xhr.open('POST', videoForm.action);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.send(formData);
        });

        function showUploadError(message) {
            submitButton.disabled = false;
            submitButton.textContent = 'Save Video';
            uploadProgress.classList.add('hidden');
            uploadProgressFill.style.width = '0%';
            formError.textContent = message;
            formError.classList.remove('hidden');
        }

        function firstValidationError(errors) {
            if (!errors) {
                return null;
            }

            const firstKey = Object.keys(errors)[0];
            return firstKey ? errors[firstKey][0] : null;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) {
                return '0 Bytes';
            }

            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const index = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, index)).toFixed(2)) + ' ' + sizes[index];
        }
    </script>
</body>

</html>
