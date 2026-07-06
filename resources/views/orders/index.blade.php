<x-app-layout title="Orders">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <span class="text-2xl text-brand font-semibold whitespace-nowrap dark:text-white">
                    Orders
                </span>
            </div>
        </div>
        <a href="{{ route('orders.create') }}" class="btn-primary">
            Create New Order
        </a>
    </div>

    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
            role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="relative overflow-x-auto">
        <table id="DataTable" class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-medium border-b border-t border-default-medium">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">Order #</th>
                    <th scope="col" class="px-6 py-3 font-medium">Table</th>
                    <th scope="col" class="px-6 py-3 font-medium">Items</th>
                    <th scope="col" class="px-6 py-3 font-medium">Total</th>
                    <th scope="col" class="px-6 py-3 font-medium">Status</th>
                    <th scope="col" class="px-6 py-3 font-medium">Date</th>
                    <th scope="col" class="px-6 py-3 font-medium text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr class="bg-neutral-brand-soft border-b border-default hover:bg-neutral-secondary-medium">
                        <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $order->table->table_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $order->orderItems->count() }} item(s)
                        </td>
                        <td class="px-6 py-4">
                            {{ number_format($order->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4">
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
                            <span class="{{ $statusColors[$order->status] ?? '' }} text-xs font-medium me-2 px-2.5 py-0.5 rounded">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            {{ $order->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('orders.show', $order) }}"
                                class="font-medium text-fg-brand hover:underline mr-3">View</a>
                            <a href="{{ route('orders.edit', $order) }}"
                                class="font-medium text-fg-brand hover:underline mr-3">Edit</a>

                            <button type="button" x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'confirm-delete-{{ $order->id }}')"
                                class="font-medium text-red-600 hover:underline">Delete</button>

                            @push('modals')
                                <x-confirm-dialog name="confirm-delete-{{ $order->id }}" title="Delete Order"
                                    :action="route('orders.destroy', $order)" method="DELETE">
                                    Are you sure you want to delete this order? This action cannot be undone.
                                </x-confirm-dialog>
                            @endpush
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('includes.data-table')

    @push('modals')
        <x-loading-dialog />
    @endpush

</x-app-layout>
