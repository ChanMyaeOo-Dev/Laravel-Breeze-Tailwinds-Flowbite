<x-app-layout title="Switch Account">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <span class="text-2xl text-brand font-semibold whitespace-nowrap dark:text-white">
                    Switch Account
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Currently logged in as <strong>{{ auth()->user()->name }}</strong>
                </span>
            </div>
        </div>
        <a href="{{ route('dashboard') }}" class="btn-secondary">
            Back to Dashboard
        </a>
    </div>

    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
            role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($restaurants as $restaurant)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:border-gray-700 dark:bg-gray-800 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-brand/10 flex items-center justify-center flex-shrink-0">
                            @if ($restaurant->logo_url)
                                <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}" class="w-12 h-12 rounded-full object-cover">
                            @else
                                <span class="text-brand font-bold text-lg">{{ strtoupper(substr($restaurant->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $restaurant->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">@{{ $restaurant->username }}</p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-1">
                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="truncate">{{ $restaurant->address }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>{{ $restaurant->phone }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ \Carbon\Carbon::parse($restaurant->opening_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($restaurant->closing_time)->format('H:i') }}</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('switch-account.show-login', $restaurant) }}" class="w-full btn-primary justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Switch to {{ $restaurant->name }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($restaurants->isEmpty())
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-lg">No other restaurants available</p>
            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">You're the only restaurant account in the system</p>
        </div>
    @endif
</x-app-layout>
