<aside class="sidebar">
  <button type="button" class="sidebar-close-btn !mt-4">
    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
  </button>
  <div>
    <a href="<?php echo e(route('manager.dashboard')); ?>" class="sidebar-logo">
      <img src="<?php echo e(asset('images/light-logo.svg')); ?>" alt="light-logo" class="light-logo">
      <img src="<?php echo e(asset('images/dark-logo.svg')); ?>" alt="dark-logo" class="dark-logo">
      <img src="<?php echo e(asset('images/small-logo.svg')); ?>" alt="small-logo" class="logo-icon">
    </a>
  </div>
  <div class="sidebar-menu-area">
    <ul class="sidebar-menu" id="sidebar-menu">
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\ManagerDashboardController::class)): ?>
        <li>
          <a href="<?php echo e(route('manager.dashboard')); ?>">
            <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('sidebar.dashboard')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Managers\SupervisorsController::class)): ?>
        <li class="<?php echo e(request()->is('managers/supervisors*') ? 'active-page show open' : ''); ?>">
          <a href="<?php echo e(route('managers.supervisors.index')); ?>" class="<?php echo e(request()->is('managers/supervisors*') ? 'active-page' : ''); ?>">
            <iconify-icon icon="mdi:account-supervisor" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('sidebar.supervisors')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Managers\DataEntryOperatorsController::class)): ?>
        <li class="<?php echo e(request()->is('managers/data-entry-operators*') ? 'active-page show open' : ''); ?>">
          <a href="<?php echo e(route('managers.data-entry-operators.index')); ?>" class="<?php echo e(request()->is('managers/data-entry-operators*') ? 'active-page' : ''); ?>">
            <iconify-icon icon="mdi:keyboard-outline" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('sidebar.data_entry_operators')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Managers\ClientsController::class)): ?>
        <li class="<?php echo e(request()->is('managers/clients*') ? 'active-page show open' : ''); ?>">
          <a href="<?php echo e(route('managers.clients.index')); ?>" class="<?php echo e(request()->is('managers/clients*') ? 'active-page' : ''); ?>">
            <iconify-icon icon="mdi:account-group" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('sidebar.clients')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Managers\DocumentsController::class)): ?>
        <li class="<?php echo e(request()->is('managers/documents*') ? 'active-page show open' : ''); ?>">
          <a href="<?php echo e(route('managers.documents.index')); ?>" class="<?php echo e(request()->is('managers/documents*') ? 'active-page' : ''); ?>">
            <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('sidebar.documents')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Managers\MessagesController::class)): ?>
        <li class="<?php echo e(request()->is('managers/messages*') ? 'active-page show open' : ''); ?>">
          <a href="<?php echo e(route('managers.messages.index')); ?>" class="<?php echo e(request()->is('managers/messages*') ? 'active-page' : ''); ?>">
            <iconify-icon icon="bi:chat-dots" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('sidebar.chat')); ?></span>
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</aside><?php /**PATH D:\xampp\htdocs\balantro\resources\views\sidebars\_manager_sidebar.blade.php ENDPATH**/ ?>