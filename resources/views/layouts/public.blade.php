<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Figtree', sans-serif; }
        </style>
    </head>
    <body class="bg-gray-50 min-h-screen">
        <div class="min-h-screen flex flex-col">
            {{-- Header --}}
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $restaurantName ?? '' }}</h1>
                        <p class="text-sm text-gray-500">Table {{ $tableNumber ?? '' }}</p>
                    </div>
                    <div class="text-sm text-gray-400">
                        Scan & Order
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 max-w-4xl mx-auto w-full px-4 py-6">
                @if (session('success'))
                    <div class="mb-6 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="bg-white border-t border-gray-200 py-4">
                <div class="max-w-4xl mx-auto px-4 text-center text-sm text-gray-400">
                    Powered by SmartServe
                </div>
            </footer>
        </div>
    </body>
</html>
