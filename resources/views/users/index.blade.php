<x-app-layout title="Users">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <span class="text-2xl text-brand font-semibold whitespace-nowrap dark:text-white">
                    Users
                </span>
            </div>
        </div>
        <a href="{{ route('users.create') }}" class="btn-primary">
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
                    <th scope="col" class="px-6 py-3 font-medium">Email</th>
                    <th scope="col" class="px-6 py-3 font-medium">Role</th>
                    <th scope="col" class="px-6 py-3 font-medium">Restaurants</th>
                    <th scope="col" class="px-6 py-3 font-medium text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="bg-neutral-brand-soft border-b border-default hover:bg-neutral-secondary-medium">
                        <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            {{ $user->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($user->is_admin)
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">Admin</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">User</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{ $user->restaurants->count() }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('users.edit', $user) }}"
                                class="font-medium text-fg-brand hover:underline mr-3">Edit</a>

                            <button type="button" x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'confirm-delete-{{ $user->id }}')"
                                class="font-medium text-red-600 hover:underline">Delete</button>

                            @push('modals')
                                <x-confirm-dialog name="confirm-delete-{{ $user->id }}" title="Delete User"
                                    :action="route('users.destroy', $user)" method="DELETE">
                                    Are you sure you want to delete this user? This action cannot be undone.
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
