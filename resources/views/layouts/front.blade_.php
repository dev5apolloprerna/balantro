<!DOCTYPE html>
<html lang="en">

<head>
    @include('includes.head')
</head>

<body class="antialiased font-sans bg-balantro-navy text-white selection:bg-balantro-primary selection:text-white">

    @include('includes.header')

    {{-- Page Content --}}
    @yield('content')

    @include('includes.footer')
    @include('includes.footer-scripts')

</body>

</html>
