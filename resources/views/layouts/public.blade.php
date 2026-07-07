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
    <body class="bg-light min-h-screen">
        <div class="min-h-screen flex flex-col">
            {{-- Header --}}
            <header class="bg-white shadow-sm border-b border-default">
                <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-dark">{{ $restaurantName ?? '' }}</h1>
                        <p class="text-sm text-body">Table {{ $tableNumber ?? '' }}</p>
                    </div>
                    <div class="text-sm text-body">
                        Scan & Order
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 max-w-4xl mx-auto w-full px-4 py-6">
                @if (session('success'))
                    <div class="mb-6 p-4 text-sm rounded-lg bg-success/10 text-success border border-success/20" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 text-sm rounded-lg bg-danger/10 text-danger border border-danger/20">
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
            <footer class="bg-white border-t border-default py-4">
                <div class="max-w-4xl mx-auto px-4 text-center text-sm text-body">
                    Powered by SmartServe
                </div>
            </footer>
        </div>
    </body>
</html>
