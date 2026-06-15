<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full">

<head>
    <title><?php echo e(config('app.name', 'Balantro')); ?> - Welcome</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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

    <link rel="icon" href="<?php echo e(asset('icon.png')); ?>" type="image/png">

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
                        dark: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#000000',
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
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease !important;
        }

        /* Ensure full height */
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        /* Remove opacity transition that causes blinking */
        body {
            opacity: 1 !important;
        }
    </style>
</head>

<body class="font-sans antialiased h-full bg-white dark:bg-black text-gray-900 dark:text-gray-100">
    <div class="min-h-full bg-white dark:bg-black">

        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white dark:bg-black border-b border-gray-200 dark:border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <a href="/" class="flex items-center">
                        <img src="<?php echo e(asset('assets/images/light-logo.svg')); ?>" alt="Balantro"
                            class="h-8 block dark:hidden">
                        <img src="<?php echo e(asset('assets/images/dark-logo.svg')); ?>" alt="Balantro"
                            class="h-8 hidden dark:block">
                    </a>

                    <div class="flex items-center gap-4">
                        <a href="<?php echo e(route('login')); ?>"
                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 rounded-lg">
                            Login
                        </a>

                        <!-- Theme Toggle -->
                        <button id="theme-toggle" type="button"
                            class="rounded-full p-2 bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-gray-800">
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

        <main class="bg-white dark:bg-black">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <script>
        // Smooth theme toggle functionality without blinking
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');

            themeToggle.addEventListener('click', () => {
                const html = document.documentElement;
                const isDark = html.classList.contains('dark');

                if (isDark) {
                    html.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    html.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            });

            // Remove the opacity initialization that causes blinking
        });
    </script>
</body>

</html>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\layouts\app.blade.php ENDPATH**/ ?>