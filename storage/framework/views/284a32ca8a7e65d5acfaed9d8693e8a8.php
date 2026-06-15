<aside class="sidebar">
  <button type="button" class="sidebar-close-btn !mt-4">
    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
  </button>
  <div>
    <a href="<?php echo e(route('super-admin.dashboard')); ?>" class="sidebar-logo">
      <img src="<?php echo e(asset('images/light-logo.svg')); ?>" alt="light-logo" class="light-logo">
      <img src="<?php echo e(asset('images/dark-logo.svg')); ?>" alt="dark-logo" class="dark-logo">
      <img src="<?php echo e(asset('images/small-logo.svg')); ?>" alt="small-logo" class="logo-icon">
    </a>
  </div>
  <div class="sidebar-menu-area">
    <ul class="sidebar-menu" id="sidebar-menu">
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\SuperAdminDashboardController::class)): ?>
        <li>
          <a href="<?php echo e(route('super-admin.dashboard')); ?>">
            <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('admin.sidebar.dashboard')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Admin\ManagersController::class)): ?>
        <li>
          <a href="<?php echo e(route('admin.managers.index')); ?>">
            <iconify-icon icon="mdi:account-tie" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('admin.sidebar.managers')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Admin\SupervisorsController::class)): ?>
        <li class="<?php echo e(request()->is('admin/supervisors*') ? 'active-page show open' : ''); ?>">
          <a href="<?php echo e(route('admin.supervisors.index')); ?>" class="<?php echo e(request()->is('admin/supervisors*') ? 'active-page' : ''); ?>">
            <iconify-icon icon="mdi:account-supervisor" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('admin.sidebar.supervisors')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Admin\DataEntryOperatorsController::class)): ?>
        <li class="<?php echo e(request()->is('admin/data-entry-operators*') ? 'active-page show open' : ''); ?>">
          <a href="<?php echo e(route('admin.data-entry-operators.index')); ?>" class="<?php echo e(request()->is('admin/data-entry-operators*') ? 'active-page' : ''); ?>">
            <iconify-icon icon="mdi:keyboard-outline" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('admin.sidebar.data_entry_operators')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Admin\ClientsController::class)): ?>
        <li class="<?php echo e(request()->is('admin/clients*') ? 'active-page show open' : ''); ?>">
          <a href="<?php echo e(route('admin.clients.index')); ?>" class="<?php echo e(request()->is('admin/clients*') ? 'active-page' : ''); ?>">
            <iconify-icon icon="mdi:account-group" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('admin.sidebar.clients')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Admin\DocumentsController::class)): ?>
        <li class="<?php echo e(request()->is('admin/documents*') ? 'active-page show open' : ''); ?>">
          <a href="<?php echo e(route('admin.documents.index')); ?>" class="<?php echo e(request()->is('admin/documents*') ? 'active-page' : ''); ?>">
            <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('admin.sidebar.documents')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Admin\GroupsController::class)): ?>
        <li>
          <a href="<?php echo e(route('admin.groups.index')); ?>">
            <iconify-icon icon="fluent:people-20-filled" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('admin.sidebar.groups')); ?></span>
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</aside><?php /**PATH D:\xampp\htdocs\balantro\resources\views\sidebars\_super_admin_sidebar.blade.php ENDPATH**/ ?>