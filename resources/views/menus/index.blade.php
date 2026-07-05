<x-app-layout title="Menus">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <span class="text-2xl text-brand font-semibold whitespace-nowrap dark:text-white">
                    Menus
                </span>
            </div>
        </div>
        <a href="{{ route('menus.create') }}" class="btn-primary">
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
                    <th scope="col" class="px-6 py-3 font-medium">Image</th>
                    <th scope="col" class="px-6 py-3 font-medium">Name</th>
                    <th scope="col" class="px-6 py-3 font-medium">Restaurant</th>
                    <th scope="col" class="px-6 py-3 font-medium">Price</th>
                    <th scope="col" class="px-6 py-3 font-medium">Status</th>
                    <th scope="col" class="px-6 py-3 font-medium text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($menus as $menu)
                    <tr class="bg-neutral-brand-soft border-b border-default hover:bg-neutral-secondary-medium">
                        <td class="px-6 py-4">
                            @if ($menu->image)
                                <img src="{{ Storage::disk(config('image.disk', 's3'))->url($menu->image) }}"
                                    alt="{{ $menu->name }}" class="w-12 h-12 object-cover rounded-lg">
                            @else
                                <div
                                    class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            {{ $menu->name }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('restaurants.edit', $menu->restaurant->id) }}" class="">
                                {{ $menu->restaurant->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            {{ $menu->price }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($menu->status)
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Available</span>
                            @else
                                <span
                                    class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Not
                                    Available</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('menus.edit', $menu) }}"
                                class="font-medium text-fg-brand hover:underline mr-3">Edit</a>

                            <button type="button" x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'confirm-delete-{{ $menu->id }}')"
                                class="font-medium text-red-600 hover:underline">Delete</button>

                            @push('modals')
                                <x-confirm-dialog name="confirm-delete-{{ $menu->id }}" title="Delete Menu"
                                    :action="route('menus.destroy', $menu)" method="DELETE">
                                    Are you sure you want to delete this menu? This action cannot be undone.
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
