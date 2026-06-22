@props(['name', 'title' => 'Confirm Action', 'action', 'method' => 'POST'])

<x-modal :name="$name" focusable>
    <form method="POST" action="{{ $action }}" class="p-6" x-on:submit="$dispatch('open-modal', 'loading-modal')">
        @csrf
        @method($method)

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ $title }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ $slot }}
        </p>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ms-3">
                {{ __('Confirm') }}
            </x-danger-button>
        </div>
    </form>
</x-modal>
