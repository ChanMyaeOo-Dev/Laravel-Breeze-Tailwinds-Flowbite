<x-app-layout title="Edit Order {{ $order->order_number }}">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <span class="text-2xl text-brand font-semibold whitespace-nowrap dark:text-white">
                    Edit Order {{ $order->order_number }}
                </span>
            </div>
        </div>
        <a href="{{ route('orders.index') }}" class="btn-secondary">
            Back to List
        </a>
    </div>

    <div
        class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <form action="{{ route('orders.update', $order) }}" method="POST" x-data=""
            x-on:submit="$dispatch('open-modal', 'loading-modal')">
            @csrf
            @method('PUT')

            <div class="grid gap-4 mb-4 sm:grid-cols-2">
                <div>
                    <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                    <select name="status" id="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        @foreach (['pending', 'preparing', 'ready', 'served', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" {{ old('status', $order->status) === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="special_instructions" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Special Instructions</label>
                    <textarea id="special_instructions" name="special_instructions" rows="3"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">{{ old('special_instructions', $order->special_instructions) }}</textarea>
                    @error('special_instructions')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn-primary">
                Update Order
            </button>
        </form>
    </div>

    @push('modals')
        <x-loading-dialog />
    @endpush
</x-app-layout>
