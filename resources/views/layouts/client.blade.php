<!DOCTYPE html>
<html>
  <head>
    <title>@yield('title', 'Balantro - Client')</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    @csrf
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('head')

    <link rel="icon" href="/icon.png" type="image/png">
    <link rel="icon" href="/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/icon.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    @auth
      <input type="hidden" id="recipient_id" value="{{ auth()->id() }}">
    @endauth
  </head>

  <body class="dark:bg-neutral-800 bg-neutral-100 dark:text-white">
    @auth
      @include('sidebars.client_sidebar')

      <!-- Main Content -->
      <main class="dashboard-main">
        <!-- Top Navigation -->
        @include('navigations.client_nav')

        <!-- Page Content -->
        <div class="dashboard-main-body">
          <div id="flashMessages">
            @if (session('notice'))
              <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('notice') }}
              </div>
            @endif
            @if (session('alert'))
              <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('alert') }}
              </div>
            @endif
          </div>

          @yield('content')
        </div>
      </main>
    @else
      @yield('content')
    @endauth
    @include('shared.common')
    @include('shared._loader')
  </body>
</html>