<!DOCTYPE html>
<html lang="en">

<head>
    @include('includes.head')
</head>

<body class="antialiased font-sans selection:bg-balantro-primary selection:text-white">
    @include('includes.header')

    {{-- Page Content --}}
    @yield('content')

    @include('includes.footer')
    @yield('scripts')

    @include('includes.footer-scripts')

</body>

</html>
