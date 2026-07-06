<x-app-layout title="Edit Table">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <span class="text-2xl text-brand font-semibold whitespace-nowrap dark:text-white">
                    Edit Table: {{ $restaurantTable->table_number }}
                </span>
            </div>
        </div>
        <a href="{{ route('restaurant-tables.index') }}" class="btn-secondary">
            Back to List
        </a>
    </div>

    <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <form action="{{ route('restaurant-tables.update', $restaurantTable) }}" method="POST" x-data="" x-on:submit="$dispatch('open-modal', 'loading-modal')">
            @csrf
            @method('PUT')
            @include('restaurant-tables.form')
            <button type="submit" class="btn-primary">
                Update Table
            </button>
        </form>
    </div>

    @push('modals')
        <x-loading-dialog />
    @endpush
</x-app-layout>
