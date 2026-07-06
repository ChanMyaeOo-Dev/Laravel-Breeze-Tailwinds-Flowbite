@php
    $user = $user ?? null;
@endphp

<div class="grid gap-4 mb-4 sm:grid-cols-2">
    <div>
        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
        <input type="text" name="name" id="name"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            value="{{ old('name', $user->name ?? '') }}" required>
        @error('name')
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
        <input type="email" name="email" id="email"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            value="{{ old('email', $user->email ?? '') }}" required>
        @error('email')
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Password {{ $user ? '(leave blank to keep current)' : '' }}
        </label>
        <input type="password" name="password" id="password"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            {{ $user ? '' : 'required' }}>
        @error('password')
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>
    <div class="flex items-end">
        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" name="is_admin" value="1" class="sr-only peer"
                {{ old('is_admin', $user->is_admin ?? false) ? 'checked' : '' }}>
            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-brand-soft dark:peer-focus:ring-brand-soft dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand">
            </div>
            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Admin</span>
        </label>
    </div>
</div>
