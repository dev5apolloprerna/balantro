<!DOCTYPE html>
<html>
  <head>
    <title>@yield('title', 'Balantro')</title>
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
  </head>

  <body class="bg-gray-50">
    @auth
      <div class="min-h-screen flex">
        <!-- Main Content -->
        <div class="flex-1">
          <!-- Top Navigation -->
          <div class="bg-white shadow">
            <div class="px-4 py-3 flex justify-between items-center">
              <h2 class="text-xl font-semibold text-gray-800">
                @yield('header', 'Dashboard')
              </h2>
              <div class="flex items-center space-x-4">
                <span class="text-gray-600">{{ auth()->user()->email }}</span>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="text-gray-600 hover:text-gray-900">Sign out</button>
                </form>
              </div>
            </div>
          </div>

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
        </div>
      </div>
    @else
      @include('navigations.public_nav')
      @include('shared.flash_messages')
      @yield('content')
    @endauth
    @include('shared.common')
    @include('shared._loader')
  </body>
</html>