<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
</head>

<body
    class="font-sans antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="min-h-screen">
        <header
            class="sticky top-0 z-50 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/" class="flex items-center">
                            <!-- Light logo -->
                            <img src="{{ asset('assets/images/light-logo.svg') }}" alt="Balantro"
                                class="h-8 block dark:hidden">
                            <!-- Dark logo -->
                            <img src="{{ asset('assets/images/dark-logo.svg') }}" alt="Balantro"
                                class="h-8 hidden dark:block">
                        </a>
                    </div>

                    <!-- Right side - Login menu and theme toggle -->
                    <div class="flex items-center gap-4">
                        <!-- Theme Toggle -->
                        <button id="theme-toggle" type="button"
                            class="rounded-full p-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-300">
                            <!-- Sun icon for light mode -->
                            <iconify-icon icon="heroicons:sun-20-solid"
                                class="w-5 h-5 text-yellow-500 block dark:hidden"></iconify-icon>
                            <!-- Moon icon for dark mode -->
                            <iconify-icon icon="heroicons:moon-20-solid"
                                class="w-5 h-5 text-indigo-400 hidden dark:block"></iconify-icon>
                            <span class="sr-only">Toggle theme</span>
                        </button>

                    </div>
                </div>
            </div>
        </header>
        <main>
            @yield('content')
        </main>
    </div>

    <script>
        // Theme toggle functionality
        document.getElementById('theme-toggle').addEventListener('click', function() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        });

        // Check for saved theme preference
        if (localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Initialize Alpine.js for dropdown functionality
        document.addEventListener('alpine:init', () => {
            Alpine.data('dropdown', () => ({
                open: false,
                toggle() {
                    this.open = !this.open;
                }
            }));
        });
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
