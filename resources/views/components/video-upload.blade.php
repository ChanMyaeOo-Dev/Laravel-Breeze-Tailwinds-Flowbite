@props([
    'name' => 'video',
    'label' => 'Video',
    'description' => 'Choose a video file to upload.',
    'buttonText' => 'Choose Video',
    'changeButtonText' => 'Choose Another Video',
    'accept' => 'video/*',
    'allowedTypes' => ['video/mp4', 'video/quicktime', 'video/webm', 'video/x-msvideo', 'video/mov'],
    'supportedFormats' => 'MP4, MOV, WebM, AVI',
    'maxUploadMb' => (int) env('VIDEO_MAX_UPLOAD_MB', 50),
    'maxUploadBytes' => null,
    'required' => false,
])

@php
    $maxUploadBytes = $maxUploadBytes ?? (int) $maxUploadMb * 1024 * 1024;
    $invalidTypeMessage = "Invalid file type. Please select {$supportedFormats} files.";
@endphp

<div {{ $attributes->merge(['class' => 'space-y-4']) }} x-data="{
    selectedFile: null,
    fileName: '',
    fileSize: '',
    fileType: '',
    error: '',
    dragover: false,
    maxUploadMb: @js((int) $maxUploadMb),
    maxUploadBytes: @js((int) $maxUploadBytes),
    allowedTypes: @js($allowedTypes),

    chooseFile() {
        this.$refs.fileInput.click();
    },

    handleFileInput(event) {
        if (event.target.files.length > 0) {
            this.handleFileSelect(event.target.files[0]);
        }
    },

    handleDrop(event) {
        this.dragover = false;

        if (event.dataTransfer.files.length === 0) {
            return;
        }

        this.$refs.fileInput.files = event.dataTransfer.files;
        this.handleFileSelect(event.dataTransfer.files[0]);
    },

    handleFileSelect(file) {
        this.error = '';

        if (!file) {
            this.reset();
            return;
        }

        if (!this.allowedTypes.includes(file.type)) {
            this.error = @js($invalidTypeMessage);
            this.$refs.fileInput.value = '';
            this.selectedFile = null;
            return;
        }

        if (file.size > this.maxUploadBytes) {
            this.error = 'File is too large. Maximum size is ' + this.maxUploadMb + 'MB.';
            this.$refs.fileInput.value = '';
            this.selectedFile = null;
            return;
        }

        this.selectedFile = file;
        this.fileName = file.name;
        this.fileSize = this.formatFileSize(file.size);
        this.fileType = file.type;
    },

    reset() {
        this.selectedFile = null;
        this.fileName = '';
        this.fileSize = '';
        this.fileType = '';
        this.error = '';
        this.$refs.fileInput.value = '';
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
    <div>
        <h3 class="text-base font-semibold text-gray-900">{{ $label }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
    </div>

    <div class="rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-5 py-8 text-center transition hover:border-brand hover:bg-teal-50"
        :class="{ 'border-brand bg-teal-50': dragover }" x-on:click="chooseFile" x-on:dragover.prevent="dragover = true"
        x-on:dragleave.prevent="dragover = false" x-on:drop.prevent="handleDrop($event)">
        <input x-ref="fileInput" type="file" name="{{ $name }}" class="hidden" accept="{{ $accept }}"
            @required($required) x-on:change="handleFileInput">

        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded bg-white text-gray-600 shadow-sm">
            <svg class="h-6 w-6" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
        </div>

        <div class="mt-4 text-sm text-gray-700">
            <button type="button" class="font-medium text-brand hover:text-brand-soft" x-on:click.stop="chooseFile">
                <span x-text="selectedFile ? @js($changeButtonText) : @js($buttonText)"></span>
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

        <button type="button"
            class="mt-4 inline-flex items-center justify-center rounded border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            x-on:click="reset">
            Remove
        </button>
    </div>

    <div x-show="error" x-cloak class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
        x-text="error"></div>
</div>
