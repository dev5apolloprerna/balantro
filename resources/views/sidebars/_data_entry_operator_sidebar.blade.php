<aside class="sidebar">
  <button type="button" class="sidebar-close-btn !mt-4">
    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
  </button>
  <div>
    <a href="{{ route('data-entry-operator.dashboard') }}" class="sidebar-logo">
      <img src="{{ asset('images/light-logo.svg') }}" alt="light-logo" class="light-logo">
      <img src="{{ asset('images/dark-logo.svg') }}" alt="dark-logo" class="dark-logo">
      <img src="{{ asset('images/small-logo.svg') }}" alt="small-logo" class="logo-icon">
    </a>
  </div>
  <div class="sidebar-menu-area">
    <ul class="sidebar-menu" id="sidebar-menu">
      @can('viewAny', App\Http\Controllers\DataEntryOperatorDashboardController::class)
        <li>
          <a href="{{ route('data-entry-operator.dashboard') }}">
            <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
            <span>{{ __('sidebar.dashboard') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\DataEntryOperators\ClientsController::class)
        <li class="{{ request()->is('data-entry-operators/clients*') ? 'active-page show open' : '' }}">
          <a href="{{ route('data-entry-operators.clients.index') }}" class="{{ request()->is('data-entry-operators/clients*') ? 'active-page' : '' }}">
            <iconify-icon icon="mdi:account-group" class="menu-icon"></iconify-icon>
            <span>{{ __('sidebar.clients') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\DataEntryOperators\DocumentsController::class)
        <li class="{{ request()->is('data-entry-operators/documents*') ? 'active-page show open' : '' }}">
          <a href="{{ route('data-entry-operators.documents.index') }}" class="{{ request()->is('data-entry-operators/documents*') ? 'active-page' : '' }}">
            <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>
            <span>{{ __('sidebar.documents') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\DataEntryOperators\MessagesController::class)
        <li class="{{ request()->is('data-entry-operators/messages*') ? 'active-page show open' : '' }}">
          <a href="{{ route('data-entry-operators.messages.index') }}" class="{{ request()->is('data-entry-operators/messages*') ? 'active-page' : '' }}">
            <iconify-icon icon="bi:chat-dots" class="menu-icon"></iconify-icon>
            <span>{{ __('sidebar.chat') }}</span>
          </a>
        </li>
      @endcan
    </ul>
  </div>
</aside>