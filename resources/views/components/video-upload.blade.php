@props([
    'name' => 'video_url',
    'pathName' => null,
    'originalNameName' => null,
    'fileSizeName' => null,
    'contentTypeName' => null,
    'value' => '',
    'pathValue' => '',
    'originalNameValue' => '',
    'fileSizeValue' => '',
    'contentTypeValue' => '',
    'label' => 'Video Upload',
    'description' => 'Upload videos directly to Supabase Storage.',
    'buttonText' => 'Upload Video',
    'uploadButtonText' => 'Upload to Supabase',
    'changeButtonText' => 'Choose Another Video',
    'accept' => 'video/*',
    'allowedTypes' => ['video/mp4', 'video/quicktime', 'video/webm', 'video/x-msvideo', 'video/mov'],
    'supportedFormats' => 'MP4, MOV, WebM, AVI',
    'maxUploadMb' => (int) env('VIDEO_MAX_UPLOAD_MB', 50),
    'maxUploadBytes' => null,
    'presignedUrl' => route('videos.presigned-url'),
    'confirmUrl' => route('videos.confirm'),
])

@php
    $maxUploadBytes = $maxUploadBytes ?? (int) $maxUploadMb * 1024 * 1024;
    $invalidTypeMessage = "Invalid file type. Please select {$supportedFormats} files.";
@endphp

<div {{ $attributes->merge(['class' => 'space-y-4']) }} x-data="{
    selectedFile: null,
    uploadedUrl: @js($value),
    uploadedPath: @js($pathValue),
    uploadedOriginalName: @js($originalNameValue),
    uploadedFileSize: @js($fileSizeValue),
    uploadedContentType: @js($contentTypeValue),
    fileName: '',
    fileSize: '',
    fileType: '',
    progress: 0,
    progressText: '',
    error: '',
    uploading: false,
    uploadStartTime: null,
    maxUploadMb: @js((int) $maxUploadMb),
    maxUploadBytes: @js((int) $maxUploadBytes),
    allowedTypes: @js($allowedTypes),
    presignedUrl: @js($presignedUrl),
    confirmUrl: @js($confirmUrl),
    csrfToken: document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '',

    chooseFile() {
        this.$refs.fileInput.click();
    },

    handleFileInput(event) {
        if (event.target.files.length > 0) {
            this.handleFileSelect(event.target.files[0]);
        }
    },

    handleDrop(event) {
        if (event.dataTransfer.files.length > 0) {
            this.handleFileSelect(event.dataTransfer.files[0]);
        }
    },

    handleFileSelect(file) {
        this.error = '';
        this.uploadedUrl = '';
        this.uploadedPath = '';
        this.uploadedOriginalName = '';
        this.uploadedFileSize = '';
        this.uploadedContentType = '';
        this.progress = 0;
        this.progressText = '';

        if (!file) {
            return;
        }

        if (!this.allowedTypes.includes(file.type)) {
            this.showError(@js($invalidTypeMessage));
            this.$refs.fileInput.value = '';
            return;
        }

        if (file.size > this.maxUploadBytes) {
            this.showError('File is too large. Maximum size is ' + this.maxUploadMb + 'MB.');
            this.$refs.fileInput.value = '';
            return;
        }

        this.selectedFile = file;
        this.fileName = file.name;
        this.fileSize = this.formatFileSize(file.size);
        this.fileType = file.type;
    },

    async startUpload() {
        if (!this.selectedFile || this.uploading) {
            return;
        }

        this.uploading = true;
        this.error = '';
        this.progress = 0;
        this.progressText = 'Getting upload URL...';
        this.uploadStartTime = Date.now();

        try {
            const response = await fetch(this.presignedUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    filename: this.selectedFile.name,
                    content_type: this.selectedFile.type,
                    file_size: this.selectedFile.size,
                }),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || this.firstValidationError(data.errors) || 'Failed to get upload URL.');
            }

            this.progressText = '0% - Uploading...';
            await this.uploadToStorage(data.presigned_url, this.selectedFile);

            const confirmResponse = await fetch(this.confirmUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    file_path: data.file_path,
                }),
            });

            const confirmData = await confirmResponse.json();

            if (!confirmResponse.ok || !confirmData.success) {
                throw new Error(confirmData.message || this.firstValidationError(confirmData.errors) || 'Failed to confirm upload.');
            }

            this.uploadedUrl = data.public_url || confirmData.url;
            this.uploadedPath = data.file_path;
            this.uploadedOriginalName = this.selectedFile.name;
            this.uploadedFileSize = confirmData.file_size || this.selectedFile.size;
            this.uploadedContentType = this.selectedFile.type;
            this.progress = 100;
            this.progressText = 'Upload complete';

            this.$dispatch('video-uploaded', {
                name: @js($name),
                url: this.uploadedUrl,
                path: this.uploadedPath,
                file: {
                    name: this.selectedFile.name,
                    size: this.selectedFile.size,
                    type: this.selectedFile.type,
                },
            });
        } catch (error) {
            this.showError(error.message || 'Upload failed. Please try again.');
            this.progress = 0;
            this.progressText = '';
        } finally {
            this.uploading = false;
        }
    },

    uploadToStorage(presignedUpload, file) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            const uploadUrl = typeof presignedUpload === 'string' ? presignedUpload : presignedUpload.url;
            const uploadHeaders = typeof presignedUpload === 'string' ? {} : (presignedUpload.headers || {});

            if (!uploadUrl) {
                reject(new Error('Upload URL was not returned by the server.'));
                return;
            }

            xhr.upload.addEventListener('progress', (event) => {
                if (!event.lengthComputable) {
                    return;
                }

                const percentComplete = Math.round((event.loaded / event.total) * 100);
                const elapsed = (Date.now() - this.uploadStartTime) / 1000;
                const speed = elapsed > 0 ? event.loaded / elapsed : 0;

                this.progress = percentComplete;
                this.progressText =
                    percentComplete + '% - ' +
                    this.formatFileSize(event.loaded) + ' / ' +
                    this.formatFileSize(event.total) +
                    ' (' + this.formatFileSize(speed) + '/s)';
            });

            xhr.addEventListener('load', () => {
                if (xhr.status === 200 || xhr.status === 204) {
                    resolve();
                    return;
                }

                let errorMessage = 'Upload failed with status ' + xhr.status;

                if (xhr.status === 413) {
                    errorMessage = 'File is larger than the Supabase Storage limit for this bucket/project.';
                }

                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (error) {}

                reject(new Error(errorMessage));
            });

            xhr.addEventListener('error', () => reject(new Error('Network error. Please check your connection and try again.')));
            xhr.addEventListener('abort', () => reject(new Error('Upload cancelled.')));
            xhr.addEventListener('timeout', () => reject(new Error('Upload timed out. The file may be too large or your connection is slow.')));

            xhr.timeout = 300000;
            xhr.open('PUT', uploadUrl);

            const unsafeHeaders = ['host', 'content-length'];
            Object.entries(uploadHeaders).forEach(([header, value]) => {
                if (!unsafeHeaders.includes(header.toLowerCase())) {
                    xhr.setRequestHeader(header, value);
                }
            });

            if (!uploadHeaders['Content-Type'] && !uploadHeaders['content-type']) {
                xhr.setRequestHeader('Content-Type', file.type);
            }

            xhr.send(file);
        });
    },

    reset() {
        this.selectedFile = null;
        this.uploadedUrl = '';
        this.uploadedPath = '';
        this.uploadedOriginalName = '';
        this.uploadedFileSize = '';
        this.uploadedContentType = '';
        this.fileName = '';
        this.fileSize = '';
        this.fileType = '';
        this.progress = 0;
        this.progressText = '';
        this.error = '';
        this.uploading = false;
        this.$refs.fileInput.value = '';
    },

    showError(message) {
        this.error = message;
    },

    firstValidationError(errors) {
        if (!errors) {
            return null;
        }

        const firstKey = Object.keys(errors)[0];
        return firstKey ? errors[firstKey][0] : null;
    },

    formatFileSize(bytes) {
        if (bytes === 0) {
            return '0 Bytes';
        }

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const index = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, index)).toFixed(2)) + ' ' + sizes[index];
    },
}">
    <input type="hidden" name="{{ $name }}" x-model="uploadedUrl">

    @if ($pathName)
        <input type="hidden" name="{{ $pathName }}" x-model="uploadedPath">
    @endif

    @if ($originalNameName)
        <input type="hidden" name="{{ $originalNameName }}" x-model="uploadedOriginalName">
    @endif

    @if ($fileSizeName)
        <input type="hidden" name="{{ $fileSizeName }}" x-model="uploadedFileSize">
    @endif

    @if ($contentTypeName)
        <input type="hidden" name="{{ $contentTypeName }}" x-model="uploadedContentType">
    @endif

    <div>
        <h3 class="text-base font-semibold text-gray-900">{{ $label }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
    </div>

    <div class="rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-5 py-8 text-center transition hover:border-brand hover:bg-teal-50"
        :class="{ 'border-brand bg-teal-50': uploading }" x-on:click="chooseFile"
        x-on:dragover.prevent="$el.classList.add('border-brand', 'bg-teal-50')"
        x-on:dragleave.prevent="$el.classList.remove('border-brand', 'bg-teal-50')"
        x-on:drop.prevent="$el.classList.remove('border-brand', 'bg-teal-50'); handleDrop($event)">
        <input x-ref="fileInput" type="file" class="hidden" accept="{{ $accept }}"
            x-on:change="handleFileInput">

        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded bg-white text-gray-600 shadow-sm">
            <svg class="h-6 w-6" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
        </div>

        <div class="mt-4 text-sm text-gray-700">
            <button type="button" class="font-medium text-brand hover:text-brand-soft" x-on:click.stop="chooseFile">
                <span
                    x-text="selectedFile || uploadedUrl ? @js($changeButtonText) : @js($buttonText)"></span>
            </button>
            <span> or drag and drop</span>
        </div>

        <p class="mt-2 text-xs text-gray-500">
            Maximum file size: {{ $maxUploadMb }}MB | Supported formats: {{ $supportedFormats }}
        </p>
    </div>

    <div x-show="selectedFile" x-cloak class="rounded border border-gray-200 bg-white p-4">
        <dl class="space-y-2 text-sm">
            <div class="flex gap-2">
                <dt class="w-24 shrink-0 font-medium text-gray-700">File Name:</dt>
                <dd class="break-all text-gray-600" x-text="fileName"></dd>
            </div>
            <div class="flex gap-2">
                <dt class="w-24 shrink-0 font-medium text-gray-700">File Size:</dt>
                <dd class="text-gray-600" x-text="fileSize"></dd>
            </div>
            <div class="flex gap-2">
                <dt class="w-24 shrink-0 font-medium text-gray-700">Type:</dt>
                <dd class="text-gray-600" x-text="fileType"></dd>
            </div>
        </dl>

        <div class="mt-4 flex flex-col gap-2 sm:flex-row">
            <button type="button" class="btn-primary justify-center" x-on:click="startUpload"
                x-bind:disabled="uploading">
                <span x-text="uploading ? 'Uploading...' : @js($uploadButtonText)"></span>
            </button>
            <button type="button"
                class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                x-on:click="reset">
                Cancel
            </button>
        </div>
    </div>

    <div x-show="progressText" x-cloak class="space-y-2">
        <div class="h-3 overflow-hidden rounded bg-gray-200">
            <div class="h-full rounded bg-success transition-all" x-bind:style="'width: ' + progress + '%'"></div>
        </div>
        <p class="text-center text-xs text-gray-500" x-text="progressText"></p>
    </div>

    <div x-show="error" x-cloak class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
        x-text="error"></div>

    <div x-show="uploadedUrl" x-cloak
        class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        <p class="font-medium">Upload Complete</p>
        <p class="mt-1 break-all text-xs" x-text="uploadedUrl"></p>
    </div>
</div>
