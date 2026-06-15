<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('theme', 'light') }}">

@include('auth.includes.NewHead')

<body
    class="font-sans antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="min-h-screen">
        <main>
            @yield('content')
        </main>
    </div>
    @include('auth.includes.NewScripts')
</body>

</html>
