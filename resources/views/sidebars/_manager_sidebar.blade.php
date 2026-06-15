<aside class="sidebar">
  <button type="button" class="sidebar-close-btn !mt-4">
    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
  </button>
  <div>
    <a href="{{ route('manager.dashboard') }}" class="sidebar-logo">
      <img src="{{ asset('images/light-logo.svg') }}" alt="light-logo" class="light-logo">
      <img src="{{ asset('images/dark-logo.svg') }}" alt="dark-logo" class="dark-logo">
      <img src="{{ asset('images/small-logo.svg') }}" alt="small-logo" class="logo-icon">
    </a>
  </div>
  <div class="sidebar-menu-area">
    <ul class="sidebar-menu" id="sidebar-menu">
      @can('viewAny', App\Http\Controllers\ManagerDashboardController::class)
        <li>
          <a href="{{ route('manager.dashboard') }}">
            <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
            <span>{{ __('sidebar.dashboard') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\Managers\SupervisorsController::class)
        <li class="{{ request()->is('managers/supervisors*') ? 'active-page show open' : '' }}">
          <a href="{{ route('managers.supervisors.index') }}" class="{{ request()->is('managers/supervisors*') ? 'active-page' : '' }}">
            <iconify-icon icon="mdi:account-supervisor" class="menu-icon"></iconify-icon>
            <span>{{ __('sidebar.supervisors') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\Managers\DataEntryOperatorsController::class)
        <li class="{{ request()->is('managers/data-entry-operators*') ? 'active-page show open' : '' }}">
          <a href="{{ route('managers.data-entry-operators.index') }}" class="{{ request()->is('managers/data-entry-operators*') ? 'active-page' : '' }}">
            <iconify-icon icon="mdi:keyboard-outline" class="menu-icon"></iconify-icon>
            <span>{{ __('sidebar.data_entry_operators') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\Managers\ClientsController::class)
        <li class="{{ request()->is('managers/clients*') ? 'active-page show open' : '' }}">
          <a href="{{ route('managers.clients.index') }}" class="{{ request()->is('managers/clients*') ? 'active-page' : '' }}">
            <iconify-icon icon="mdi:account-group" class="menu-icon"></iconify-icon>
            <span>{{ __('sidebar.clients') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\Managers\DocumentsController::class)
        <li class="{{ request()->is('managers/documents*') ? 'active-page show open' : '' }}">
          <a href="{{ route('managers.documents.index') }}" class="{{ request()->is('managers/documents*') ? 'active-page' : '' }}">
            <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>
            <span>{{ __('sidebar.documents') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\Managers\MessagesController::class)
        <li class="{{ request()->is('managers/messages*') ? 'active-page show open' : '' }}">
          <a href="{{ route('managers.messages.index') }}" class="{{ request()->is('managers/messages*') ? 'active-page' : '' }}">
            <iconify-icon icon="bi:chat-dots" class="menu-icon"></iconify-icon>
            <span>{{ __('sidebar.chat') }}</span>
          </a>
        </li>
      @endcan
    </ul>
  </div>
</aside>