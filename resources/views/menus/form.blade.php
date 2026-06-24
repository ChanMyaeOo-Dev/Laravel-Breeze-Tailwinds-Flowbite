@php
    $menu = $menu ?? null;
@endphp

<div class="grid gap-4 mb-4 sm:grid-cols-2">
    <div>
        <label for="restaurant_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Restaurant</label>
        <select name="restaurant_id" id="restaurant_id"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            required>
            <option value="">Select Restaurant</option>
            @foreach (\App\Models\Restaurant::orderBy('name')->get() as $restaurant)
                <option value="{{ $restaurant->id }}"
                    {{ old('restaurant_id', $menu->restaurant_id ?? '') == $restaurant->id ? 'selected' : '' }}>
                    {{ $restaurant->name }}
                </option>
            @endforeach
        </select>
        @error('restaurant_id')
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Menu Name</label>
        <input type="text" name="name" id="name"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            value="{{ old('name', $menu->name ?? '') }}" required>
        @error('name')
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Slug</label>
        <input type="text" name="slug" id="slug"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            value="{{ old('slug', $menu->slug ?? '') }}" required>
        @error('slug')
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Price</label>
        <input type="text" name="price" id="price"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            value="{{ old('price', $menu->price ?? '') }}" required>
        @error('price')
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Type</label>
        <input type="text" name="type" id="type"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            value="{{ old('type', $menu->type ?? '') }}" placeholder="e.g. Appetizer, Main Course, Dessert" required>
        @error('type')
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>
    <div class="sm:col-span-2">
        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
        <textarea id="description" name="description" rows="3"
            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">{{ old('description', $menu->description ?? '') }}</textarea>
        @error('description')
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>
    <div class="sm:col-span-2">
        <x-image-upload name="image" label="Menu Image" :existing="$menu->image ?? null" />
    </div>
    <div class="sm:col-span-2">
        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" name="status" value="1" class="sr-only peer"
                {{ old('status', $menu->status ?? true) ? 'checked' : '' }}>
            <div
                class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-brand-soft dark:peer-focus:ring-brand-soft dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand">
            </div>
            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Active</span>
        </label>
    </div>
</div>
