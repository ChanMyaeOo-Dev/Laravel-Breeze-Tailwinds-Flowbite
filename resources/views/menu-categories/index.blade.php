<x-app-layout title="Menu Categories">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <span class="text-2xl text-brand font-semibold whitespace-nowrap dark:text-white">
                    Menu Categories
                </span>
            </div>
        </div>
        <a href="{{ route('menu-categories.create') }}" class="btn-primary">
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
                    <th scope="col" class="px-6 py-3 font-medium">Name</th>
                    <th scope="col" class="px-6 py-3 font-medium">Description</th>
                    <th scope="col" class="px-6 py-3 font-medium">Display Order</th>
                    <th scope="col" class="px-6 py-3 font-medium text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($menuCategories as $category)
                    <tr class="bg-neutral-brand-soft border-b border-default hover:bg-neutral-secondary-medium">
                        <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            {{ $category->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $category->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $category->display_order }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('menu-categories.edit', $category) }}"
                                class="font-medium text-fg-brand hover:underline mr-3">Edit</a>

                            <button type="button" x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'confirm-delete-{{ $category->id }}')"
                                class="font-medium text-red-600 hover:underline">Delete</button>

                            @push('modals')
                                <x-confirm-dialog name="confirm-delete-{{ $category->id }}" title="Delete Menu Category"
                                    :action="route('menu-categories.destroy', $category)" method="DELETE">
                                    Are you sure you want to delete this category? This action cannot be undone.
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
