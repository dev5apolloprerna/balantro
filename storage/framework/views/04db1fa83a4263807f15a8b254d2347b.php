<head>
    <title><?php echo e(config('app.name', 'Balantro')); ?> - <?php echo $__env->yieldContent('title', 'Page'); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="csrf-param" content="authenticity_token" />

    <link rel="icon" href="<?php echo e(asset('icon.png')); ?>" type="image/png">
    <link rel="icon" href="<?php echo e(asset('icon.svg')); ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="<?php echo e(asset('icon.png')); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme') || localStorage.getItem('color-theme');
            const isDark = savedTheme ? savedTheme === 'dark' : true;
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>
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
                        },
                        secondary: {
                            light: '#6b7280',
                        }
                    }
                }
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>

    <style>
        /* Critical CSS to prevent blinking */
        .dark {
            color-scheme: dark;
        }

        /* Apply transitions only after initial load */
        .theme-transition * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        /* Pure Black dark mode overrides */
        .dark .bg-black {
            background-color: #000000 !important;
        }

        .dark .bg-gray-900 {
            background-color: #000000 !important;
        }

        .dark body {
            background-color: #000000 !important;
        }

        .dark main {
            background-color: #000000 !important;
        }
    </style>
</head>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views/auth/includes/head.blade.php ENDPATH**/ ?>