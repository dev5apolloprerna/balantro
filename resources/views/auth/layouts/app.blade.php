<!-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light"> -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- Remove the session theme class initially to prevent blink -->

@include('auth.includes.head')

<body class="font-sans antialiased bg-white dark:bg-black text-gray-900 dark:text-gray-100">
    <!-- Remove transition classes from body to prevent initial blink -->
    <div class="min-h-screen">
        <main>
            @yield('content')
        </main>
    </div>
    @include('auth.includes.scripts')
</body>

</html>
