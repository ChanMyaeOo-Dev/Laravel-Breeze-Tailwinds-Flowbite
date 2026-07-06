<x-app-layout title="Switch Account - {{ $restaurant->name }}">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <span class="text-2xl text-brand font-semibold whitespace-nowrap dark:text-white">
                    Switch to {{ $restaurant->name }}
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Enter the restaurant's credentials to switch
                </span>
            </div>
        </div>
        <a href="{{ route('switch-account') }}" class="btn-secondary">
            Back to List
        </a>
    </div>

    <div class="max-w-md mx-auto">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:border-gray-700 dark:bg-gray-800 overflow-hidden">
            {{-- Restaurant Info --}}
            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-brand/10 flex items-center justify-center flex-shrink-0">
                        @if ($restaurant->logo_url)
                            <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}" class="w-14 h-14 rounded-full object-cover">
                        @else
                            <span class="text-brand font-bold text-xl">{{ strtoupper(substr($restaurant->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white text-lg">{{ $restaurant->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">@{{ $restaurant->username }}</p>
                    </div>
                </div>
            </div>

            {{-- Login Form --}}
            <div class="p-5">
                <form method="POST" action="{{ route('switch-account.switch', $restaurant) }}">
                    @csrf

                    <div class="mb-4">
                        <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username', $restaurant->username) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            required autofocus autocomplete="username">
                        @error('username')
                            <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                        <input type="password" name="password" id="password"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            required autocomplete="current-password">
                        @error('password')
                            <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="w-full btn-primary justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Switch to {{ $restaurant->name }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
