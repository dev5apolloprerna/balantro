<aside class="sidebar">
  <button type="button" class="sidebar-close-btn !mt-4">
    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
  </button>
  <div>
    <a href="{{ route('client.dashboard') }}" class="sidebar-logo">
      <img src="{{ asset('images/light-logo.svg') }}" alt="light-logo" class="light-logo">
      <img src="{{ asset('images/dark-logo.svg') }}" alt="dark-logo" class="dark-logo">
      <img src="{{ asset('images/small-logo.svg') }}" alt="small-logo" class="logo-icon">
    </a>
  </div>
  <div class="sidebar-menu-area">
    <ul class="sidebar-menu" id="sidebar-menu">
      @can('viewAny', App\Http\Controllers\ClientDashboardController::class)
        <li>
          <a href="{{ route('client.dashboard') }}">
            <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
            <span>{{ __('client.sidebar.menu.dashboard') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\DocumentsController::class)
        <li class="{{ request()->is('documents*') ? 'active-page show open' : '' }}">
          <a href="{{ route('documents.index') }}" class="{{ request()->is('documents*') ? 'active-page' : '' }}">
            <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>
            <span>{{ __('client.sidebar.menu.documents') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\Client\MessagesController::class)
        @if(auth()->user()->data_entry_operators->count() > 0)
          <li class="{{ request()->is('client/messages*') ? 'active-page show open' : '' }}">
            <a href="{{ route('client.messages.index') }}" class="{{ request()->is('client/messages*') ? 'active-page' : '' }}">
              <iconify-icon icon="bi:chat-dots" class="menu-icon"></iconify-icon>
              <span>{{ __('sidebar.chat') }}</span>
            </a>
          </li>
        @endif
      @endcan
    </ul>
  </div>
</aside>