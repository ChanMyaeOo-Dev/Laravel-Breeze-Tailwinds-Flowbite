@props([
    'name',
    'label' => 'Upload Image',
    'existing' => null,
    'accept' => 'image/jpeg,image/png,image/webp,image/gif',
    'maxSize' => null,
])

@php
    $maxSize = $maxSize ?? config('image.max_file_size', 5);
    $existingUrl = $existing ? Storage::disk(config('image.disk', 's3'))->url($existing) : null;
    $maxSizeBytes = $maxSize * 1024 * 1024;
@endphp

<div
    x-data="{
        preview: '{{ $existingUrl }}',
        fileName: '',
        error: '',
        isDragging: false,
        maxSize: {{ $maxSizeBytes }},
        handleDrop(e) {
            this.isDragging = false;
            const file = e.dataTransfer.files[0];
            if (file) this.processFile(file);
        },
        handleChange(e) {
            const file = e.target.files[0];
            if (file) this.processFile(file);
        },
        processFile(file) {
            this.error = '';
            if (!file.type.startsWith('image/')) {
                this.error = 'Please select a valid image file.';
                return;
            }
            if (file.size > this.maxSize) {
                this.error = 'File size must be less than {{ $maxSize }}MB.';
                return;
            }
            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (e) => { this.preview = e.target.result; };
            reader.readAsDataURL(file);
        },
        removeImage() {
            this.preview = '{{ $existingUrl }}';
            this.fileName = '';
            this.error = '';
            this.$refs.fileInput.value = '';
        }
    }"
    class="w-full"
>
    <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        {{ $label }}
    </label>

    <div
        class="relative border-2 border-dashed rounded-lg cursor-pointer transition-colors
            {{ 'border-gray-300 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600' }}"
        :class="{ 'border-blue-500 bg-blue-50 dark:bg-blue-900/20': isDragging }"
        x-on:dragover.prevent="isDragging = true"
        x-on:dragleave.prevent="isDragging = false"
        x-on:drop.prevent="handleDrop($event)"
        x-on:click="$refs.fileInput.click()"
    >
        <input
            type="file"
            name="{{ $name }}"
            id="{{ $name }}"
            accept="{{ $accept }}"
            x-ref="fileInput"
            class="hidden"
            x-on:change="handleChange($event)"
        >

        {{-- Preview --}}
        <div x-show="preview" class="relative p-4">
            <img
                :src="preview"
                alt="Preview"
                class="w-full max-h-64 object-contain rounded-lg"
            >
            <button
                type="button"
                x-on:click.stop="removeImage()"
                class="absolute top-6 right-6 bg-red-600 text-white rounded-full p-1.5 hover:bg-red-700 transition-colors shadow-lg"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <p x-show="fileName" class="mt-2 text-sm text-gray-500 dark:text-gray-400 text-center" x-text="fileName"></p>
        </div>

        {{-- Placeholder --}}
        <div x-show="!preview" class="flex flex-col items-center justify-center py-12 px-4">
            <svg class="w-12 h-12 mb-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M3 7.5A1.5 1.5 0 014.5 6h15A1.5 1.5 0 0121 7.5v9a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 16.5v-9zM12 10.5v6m-3-3l3-3 3 3"/>
            </svg>
            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                <span class="font-semibold">Click to upload</span> or drag and drop
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500">
                JPG, PNG, WebP or GIF (max. {{ $maxSize }}MB)
            </p>
        </div>
    </div>

    {{-- Error --}}
    <p x-show="error" class="mt-2 text-sm text-red-600" x-text="error"></p>
    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
