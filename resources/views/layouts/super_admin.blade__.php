<!DOCTYPE html>
<html class="h-full">

<head>
    <title>@yield('title', 'Balantro - Super Admin')</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    @csrf
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('common.head')

    <!-- Tailwind -->
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
        };
    </script>

    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    
    <style>
        /* Force pure black backgrounds */
        .dark body {
            background-color: #000000 !important;
        }

        .dark .min-h-screen {
            background-color: #000000 !important;
        }

        .dark .main-content {
            background-color: #000000 !important;
        }

        .dark main {
            background-color: #000000 !important;
        }

        .dark .dashboard-main-body {
            background-color: #000000 !important;
        }

        /* Smooth transitions for theme switching */
        body,
        header,
        .theme-transition {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
    </style>

</head>

<body class="font-sans antialiased h-full bg-white dark:bg-black text-gray-900 dark:text-gray-100">
    <div class="flex min-h-screen bg-white dark:bg-black">
        @include('common.sidebars')
        <!-- <div class="main-content theme-transition flex-1 overflow-hidden bg-white dark:bg-black" id="main-content"> -->
        <div class="main-content flex-1 overflow-hidden">

            @include('common.header')
            <!-- Main Content -->
            <!-- Page Content -->
            <!-- <main class="p-3 overflow-y-auto bg-white dark:bg-black" style="height: calc(100vh - 80px)"> -->
            <main class="p-3 bg-white dark:bg-black">
                <!-- Welcome Header -->

                <div class="dashboard-main-body bg-white dark:bg-black">
                    <div id="flashMessages">
                        @if (session('notice'))
                            <div
                                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 dark:bg-green-900 dark:border-green-700 dark:text-green-300">
                                {{ session('notice') }}
                            </div>
                        @endif
                        @if (session('alert'))
                            <div
                                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 dark:bg-red-900 dark:border-red-700 dark:text-red-300">
                                {{ session('alert') }}
                            </div>
                        @endif
                    </div>

                    @yield('content')
                    <!-- jQuery -->
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                    <!-- Select2 -->
                    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> -->
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

                    @include('common.footer')

                    @yield('scripts')
                    @stack('scripts')
                </div>
            </main>
        </div>
    </div>
    
</body>

</html>
