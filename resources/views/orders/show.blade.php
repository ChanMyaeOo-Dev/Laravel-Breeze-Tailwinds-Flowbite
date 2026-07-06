<x-app-layout title="Order {{ $order->order_number }}">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <span class="text-2xl text-brand font-semibold whitespace-nowrap dark:text-white">
                    Order {{ $order->order_number }}
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('orders.edit', $order) }}" class="btn-secondary">
                Edit
            </a>
            <a href="{{ route('orders.index') }}" class="btn-secondary">
                Back to List
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
            role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Order Details</h3>

            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                    <dd>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                'preparing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                'ready' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                'served' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                            ];
                        @endphp
                        <span class="{{ $statusColors[$order->status] ?? '' }} text-xs font-medium px-2.5 py-0.5 rounded">
                            {{ ucfirst($order->status) }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Date</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">{{ $order->created_at->format('M d, Y H:i') }}</dd>
                </div>
                @if ($order->special_instructions)
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Instructions</dt>
                        <dd class="text-sm text-gray-900 dark:text-white text-right max-w-xs">{{ $order->special_instructions }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Order Summary</h3>

            <dl class="space-y-2">
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500 dark:text-gray-400">Subtotal</dt>
                    <dd class="text-gray-900 dark:text-white">{{ number_format($order->subtotal, 2) }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500 dark:text-gray-400">Tax (10%)</dt>
                    <dd class="text-gray-900 dark:text-white">{{ number_format($order->tax_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between text-base font-semibold pt-2 border-t">
                    <dt>Total</dt>
                    <dd>{{ number_format($order->total_amount, 2) }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-4 p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Order Items</h3>

        @if ($order->orderItems->count() > 0)
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-body">
                    <thead class="text-sm text-body bg-neutral-secondary-medium border-b border-t border-default-medium">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">Item</th>
                            <th scope="col" class="px-6 py-3 font-medium">Price</th>
                            <th scope="col" class="px-6 py-3 font-medium">Qty</th>
                            <th scope="col" class="px-6 py-3 font-medium">Subtotal</th>
                            <th scope="col" class="px-6 py-3 font-medium">Status</th>
                            <th scope="col" class="px-6 py-3 font-medium">Notes</th>
                            <th scope="col" class="px-6 py-3 font-medium text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderItems as $item)
                            <tr class="bg-neutral-brand-soft border-b border-default hover:bg-neutral-secondary-medium">
                                <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                    {{ $item->menu->name }}
                                </td>
                                <td class="px-6 py-4">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-4">{{ $item->quantity }}</td>
                                <td class="px-6 py-4">{{ number_format($item->subtotal, 2) }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $itemStatusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                            'preparing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                            'ready' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                            'served' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
                                            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        ];
                                    @endphp
                                    <span class="{{ $itemStatusColors[$item->status] ?? '' }} text-xs font-medium px-2.5 py-0.5 rounded">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $item->notes ?? '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button" x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-delete-item-{{ $item->id }}')"
                                        class="font-medium text-red-600 hover:underline">Remove</button>

                                    @push('modals')
                                        <x-confirm-dialog name="confirm-delete-item-{{ $item->id }}" title="Remove Item"
                                            :action="route('orders.order-items.destroy', [$order, $item])" method="DELETE">
                                            Are you sure you want to remove this item from the order?
                                        </x-confirm-dialog>
                                    @endpush
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500 text-center py-4">No items in this order.</p>
        @endif
    </div>

    @push('modals')
        <x-loading-dialog />
    @endpush

</x-app-layout>
