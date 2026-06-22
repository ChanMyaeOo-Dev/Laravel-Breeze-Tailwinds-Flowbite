<x-app-layout title="Restaurants">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <span class="text-2xl text-brand font-semibold whitespace-nowrap dark:text-white">
                    Restaurants
                </span>
            </div>
        </div>
        <a href="{{ route('restaurants.create') }}" class="btn-primary">
            Create New
        </a>
    </div>

    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="relative overflow-x-auto">
        <table id="DataTable" class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-medium border-b border-t border-default-medium">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">Name</th>
                    <th scope="col" class="px-6 py-3 font-medium">Username</th>
                    <th scope="col" class="px-6 py-3 font-medium">Phone</th>
                    <th scope="col" class="px-6 py-3 font-medium">Hours</th>
                    <th scope="col" class="px-6 py-3 font-medium">Status</th>
                    <th scope="col" class="px-6 py-3 font-medium text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($restaurants as $restaurant)
                    <tr class="bg-neutral-brand-soft border-b border-default hover:bg-neutral-secondary-medium">
                        <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            {{ $restaurant->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $restaurant->username }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $restaurant->phone }}
                        </td>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($restaurant->opening_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($restaurant->closing_time)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($restaurant->is_active)
                                <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Active</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('restaurants.edit', $restaurant) }}" class="font-medium text-fg-brand hover:underline mr-3">Edit</a>
                            
                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-delete-{{ $restaurant->id }}')" class="font-medium text-red-600 hover:underline">Delete</button>

                            @push('modals')
                                <x-confirm-dialog name="confirm-delete-{{ $restaurant->id }}" title="Delete Restaurant" :action="route('restaurants.destroy', $restaurant)" method="DELETE">
                                    Are you sure you want to delete this restaurant? This action cannot be undone.
                                </x-confirm-dialog>
                            @endpush
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @push('modals')
        <x-loading-dialog />
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.3.8/js/dataTables.tailwindcss.js"></script>
        <script>
            let table = new DataTable('#DataTable');
        </script>
    @endpush

</x-app-layout>
