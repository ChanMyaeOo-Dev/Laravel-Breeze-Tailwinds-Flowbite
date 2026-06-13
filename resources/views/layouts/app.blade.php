<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- brand Meta Tags -->
    <title>{{ env('APP_NAME', 'Admin Dashboard - CMO') }}</title>
    <meta name="title" content="{{ env('APP_NAME', 'Admin Dashboard - CMO') }}" />
    <meta name="description" content="Admin Dashboard template using Laravel, Tailwinds, Blade and Flowbite." />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ env('APP_URL', 'Admin Dashboard - CMO') }}" />
    <meta property="og:title" content="{{ env('APP_NAME', 'Admin Dashboard - CMO') }}" />
    <meta property="og:description" content="Admin Dashboard template using Laravel, Tailwinds, Blade and Flowbite." />
    <meta property="og:image" content="https://metatags.io/images/meta-tags.png" />

    <!-- X (Twitter) -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="{{ env('APP_URL', 'Admin Dashboard - CMO') }}" />
    <meta property="twitter:title" content="{{ env('APP_NAME', 'Admin Dashboard - CMO') }}" />
    <meta property="twitter:description"
        content="Admin Dashboard template using Laravel, Tailwinds, Blade and Flowbite." />
    <meta property="twitter:image" content="https://metatags.io/images/meta-tags.png" />

    {{-- Fav Icon --}}
    <link rel="icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/favicon.ico">
    {{-- <link rel="manifest" href="/site.webmanifest"> --}}
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

</head>

<body>
    <x-side-bar />
    <div class="antialiased gradient-bg fixed top-0 left-0 w-full h-full">
        <main class="relative z-0 p-4 md:ml-64 h-screen max-h-screen">
            <div class="w-full h-full bg-white rounded">
                <div
                    class="bg-white border border-neutral-300 rounded h-full p-6 overflow-scroll scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                    {{-- <x-nav-bar /> --}}
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>
    <script src="{{ asset('assets/js/flowbite.min.js') }}"></script>
    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Change the icons inside the button based on previous settings
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {

            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }

            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }

        });
    </script>
    @stack('scripts')
</body>

</html>
