<x-app-layout title="Tables">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <span class="text-2xl text-brand font-semibold whitespace-nowrap dark:text-white">
                    Tables
                </span>
            </div>
        </div>
        <a href="{{ route('restaurant-tables.create') }}" class="btn-primary">
            Create New
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
                    <th scope="col" class="px-6 py-3 font-medium">Table #</th>
                    <th scope="col" class="px-6 py-3 font-medium">Section</th>
                    <th scope="col" class="px-6 py-3 font-medium">Capacity</th>
                    <th scope="col" class="px-6 py-3 font-medium">Status</th>
                    <th scope="col" class="px-6 py-3 font-medium">QR Code</th>
                    <th scope="col" class="px-6 py-3 font-medium text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($restaurantTables as $table)
                    <tr class="bg-neutral-brand-soft border-b border-default hover:bg-neutral-secondary-medium">
                        <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            {{ $table->table_number }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $table->section ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $table->seating_capacity }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($table->status === 'available')
                                <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Available</span>
                            @elseif ($table->status === 'occupied')
                                <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Occupied</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Reserved</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($table->qr_code_image)
                                <div x-data="{ open: false }">
                                    <button type="button" @click="open = true" class="text-fg-brand hover:underline text-sm font-medium flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View QR
                                    </button>

                                    <template x-if="open">
                                        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="open = false">
                                            <div class="bg-white rounded-xl shadow-xl p-6 max-w-sm w-full mx-4">
                                                <div class="flex items-center justify-between mb-4">
                                                    <h3 class="text-lg font-semibold text-gray-900">Table {{ $table->table_number }}</h3>
                                                    <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                                <div class="flex justify-center mb-4">
                                                    <img src="{{ $table->qr_code_url }}" alt="QR Code for Table {{ $table->table_number }}" class="w-48 h-48">
                                                </div>
                                                <div class="flex gap-2">
                                                    <a href="{{ $table->qr_code_url }}" download="table-{{ $table->table_number }}-qr.png"
                                                        class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                                                        Download
                                                    </a>
                                                    <a href="{{ route('public.order.form', $table->qr_code) }}" target="_blank"
                                                        class="flex-1 text-center bg-brand hover:bg-brand-soft text-white font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                                                        Preview Order Page
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">No QR code</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('restaurant-tables.edit', $table) }}"
                                class="font-medium text-fg-brand hover:underline mr-3">Edit</a>

                            <button type="button" x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'confirm-delete-{{ $table->id }}')"
                                class="font-medium text-red-600 hover:underline">Delete</button>

                            @push('modals')
                                <x-confirm-dialog name="confirm-delete-{{ $table->id }}" title="Delete Table"
                                    :action="route('restaurant-tables.destroy', $table)" method="DELETE">
                                    Are you sure you want to delete this table? This action cannot be undone.
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
