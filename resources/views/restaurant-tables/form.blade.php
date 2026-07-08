@php
    $restaurantTable = $restaurantTable ?? null;
@endphp

<div class="grid gap-4 mb-4 sm:grid-cols-2">
    <div>
        <label for="table_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Table Number</label>
        <input type="text" name="table_number" id="table_number"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            value="{{ old('table_number', $restaurantTable->table_number ?? '') }}" required>
        @error('table_number')
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="seating_capacity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Seating Capacity</label>
        <input type="number" name="seating_capacity" id="seating_capacity" min="1"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            value="{{ old('seating_capacity', $restaurantTable->seating_capacity ?? 4) }}" required>
        @error('seating_capacity')
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
        <select name="status" id="status"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
            <option value="available" {{ old('status', $restaurantTable->status ?? 'available') === 'available' ? 'selected' : '' }}>Available</option>
            <option value="occupied" {{ old('status', $restaurantTable->status ?? '') === 'occupied' ? 'selected' : '' }}>Occupied</option>
            <option value="reserved" {{ old('status', $restaurantTable->status ?? '') === 'reserved' ? 'selected' : '' }}>Reserved</option>
        </select>
        @error('status')
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror
    </div>
</div>
