<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">

<head>
    <title>{{ config('app.name', 'Balantro') }} - Welcome</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">

    <!-- ✅ Apply dark mode instantly before CSS -->
    <script>
        (function() {
            const storedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (storedTheme === 'dark' || (!storedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <link rel="icon" href="{{ asset('icon.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        matblack: {
                            50: '#f8f9fa',
                            100: '#e9ecef',
                            200: '#dee2e6',
                            300: '#ced4da',
                            400: '#adb5bd',
                            500: '#6c757d',
                            600: '#495057',
                            700: '#343a40',
                            800: '#212529',
                            900: '#121212', // True Mat Black
                        }
                    }
                }
            }
        };
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>

    <style>
        /* Smooth transitions for theme switching */
        body,
        header,
        .theme-transition {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
    </style>
</head>

<body class="font-sans antialiased bg-white dark:bg-matblack-900 text-gray-900 dark:text-gray-100">
    <div class="min-h-screen">

        <!-- Header -->
        <header
            class="sticky top-0 z-50 bg-white dark:bg-matblack-900 border-b border-gray-200 dark:border-gray-800 theme-transition">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <a href="/" class="flex items-center">
                        <img src="{{ asset('assets/images/light-logo.svg') }}" alt="Balantro"
                            class="h-8 block dark:hidden">
                        <img src="{{ asset('assets/images/dark-logo.svg') }}" alt="Balantro"
                            class="h-8 hidden dark:block">
                    </a>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('login') }}"
                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 rounded-lg">
                            Login
                        </a>

                        <!-- Theme Toggle -->
                        <button id="theme-toggle" type="button"
                            class="rounded-full p-2 bg-gray-100 dark:bg-matblack-800 hover:bg-gray-200 dark:hover:bg-matblack-700 transition-all duration-200 theme-transition">
                            <iconify-icon icon="heroicons:sun-20-solid"
                                class="w-5 h-5 text-yellow-500 block dark:hidden"></iconify-icon>
                            <iconify-icon icon="heroicons:moon-20-solid"
                                class="w-5 h-5 text-indigo-400 hidden dark:block"></iconify-icon>
                            <span class="sr-only">Toggle theme</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <main class="theme-transition">
            @yield('content')
        </main>
    </div>

    <script>
        // Enhanced theme toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');

            themeToggle.addEventListener('click', () => {
                const html = document.documentElement;
                const isDark = html.classList.contains('dark');

                // Add transition class to body for smooth transition
                document.body.classList.add('theme-transition');

                if (isDark) {
                    html.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    html.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }

                // Remove transition class after transition completes
                setTimeout(() => {
                    document.body.classList.remove('theme-transition');
                }, 300);
            });

            // Initialize body transition after DOM loads
            document.body.style.opacity = '0';
            setTimeout(() => {
                document.body.style.transition = 'opacity 0.2s ease';
                document.body.style.opacity = '1';
            }, 10);
        });
    </script>
</body>

</html>
