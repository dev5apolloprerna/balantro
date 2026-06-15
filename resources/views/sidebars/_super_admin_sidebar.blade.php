<aside class="sidebar">
  <button type="button" class="sidebar-close-btn !mt-4">
    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
  </button>
  <div>
    <a href="{{ route('super-admin.dashboard') }}" class="sidebar-logo">
      <img src="{{ asset('images/light-logo.svg') }}" alt="light-logo" class="light-logo">
      <img src="{{ asset('images/dark-logo.svg') }}" alt="dark-logo" class="dark-logo">
      <img src="{{ asset('images/small-logo.svg') }}" alt="small-logo" class="logo-icon">
    </a>
  </div>
  <div class="sidebar-menu-area">
    <ul class="sidebar-menu" id="sidebar-menu">
      @can('viewAny', App\Http\Controllers\SuperAdminDashboardController::class)
        <li>
          <a href="{{ route('super-admin.dashboard') }}">
            <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
            <span>{{ __('admin.sidebar.dashboard') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\Admin\ManagersController::class)
        <li>
          <a href="{{ route('admin.managers.index') }}">
            <iconify-icon icon="mdi:account-tie" class="menu-icon"></iconify-icon>
            <span>{{ __('admin.sidebar.managers') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\Admin\SupervisorsController::class)
        <li class="{{ request()->is('admin/supervisors*') ? 'active-page show open' : '' }}">
          <a href="{{ route('admin.supervisors.index') }}" class="{{ request()->is('admin/supervisors*') ? 'active-page' : '' }}">
            <iconify-icon icon="mdi:account-supervisor" class="menu-icon"></iconify-icon>
            <span>{{ __('admin.sidebar.supervisors') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\Admin\DataEntryOperatorsController::class)
        <li class="{{ request()->is('admin/data-entry-operators*') ? 'active-page show open' : '' }}">
          <a href="{{ route('admin.data-entry-operators.index') }}" class="{{ request()->is('admin/data-entry-operators*') ? 'active-page' : '' }}">
            <iconify-icon icon="mdi:keyboard-outline" class="menu-icon"></iconify-icon>
            <span>{{ __('admin.sidebar.data_entry_operators') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\Admin\ClientsController::class)
        <li class="{{ request()->is('admin/clients*') ? 'active-page show open' : '' }}">
          <a href="{{ route('admin.clients.index') }}" class="{{ request()->is('admin/clients*') ? 'active-page' : '' }}">
            <iconify-icon icon="mdi:account-group" class="menu-icon"></iconify-icon>
            <span>{{ __('admin.sidebar.clients') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\Admin\DocumentsController::class)
        <li class="{{ request()->is('admin/documents*') ? 'active-page show open' : '' }}">
          <a href="{{ route('admin.documents.index') }}" class="{{ request()->is('admin/documents*') ? 'active-page' : '' }}">
            <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>
            <span>{{ __('admin.sidebar.documents') }}</span>
          </a>
        </li>
      @endcan

      @can('viewAny', App\Http\Controllers\Admin\GroupsController::class)
        <li>
          <a href="{{ route('admin.groups.index') }}">
            <iconify-icon icon="fluent:people-20-filled" class="menu-icon"></iconify-icon>
            <span>{{ __('admin.sidebar.groups') }}</span>
          </a>
        </li>
      @endcan
    </ul>
  </div>
</aside>