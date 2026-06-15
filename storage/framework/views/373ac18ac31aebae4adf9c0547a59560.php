<aside class="sidebar">
  <button type="button" class="sidebar-close-btn !mt-4">
    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
  </button>
  <div>
    <a href="<?php echo e(route('supervisor.dashboard')); ?>" class="sidebar-logo">
      <img src="<?php echo e(asset('images/light-logo.svg')); ?>" alt="light-logo" class="light-logo">
      <img src="<?php echo e(asset('images/dark-logo.svg')); ?>" alt="dark-logo" class="dark-logo">
      <img src="<?php echo e(asset('images/small-logo.svg')); ?>" alt="small-logo" class="logo-icon">
    </a>
  </div>
  <div class="sidebar-menu-area">
    <ul class="sidebar-menu" id="sidebar-menu">
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\SupervisorDashboardController::class)): ?>
        <li>
          <a href="<?php echo e(route('supervisor.dashboard')); ?>">
            <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('sidebar.dashboard')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Supervisors\DataEntryOperatorsController::class)): ?>
        <li class="<?php echo e(request()->is('supervisors/data-entry-operators*') ? 'active-page show open' : ''); ?>">
          <a href="<?php echo e(route('supervisors.data-entry-operators.index')); ?>" class="<?php echo e(request()->is('supervisors/data-entry-operators*') ? 'active-page' : ''); ?>">
            <iconify-icon icon="mdi:keyboard-outline" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('sidebar.data_entry_operators')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Supervisors\ClientsController::class)): ?>
        <li class="<?php echo e(request()->is('supervisors/clients*') ? 'active-page show open' : ''); ?>">
          <a href="<?php echo e(route('supervisors.clients.index')); ?>" class="<?php echo e(request()->is('supervisors/clients*') ? 'active-page' : ''); ?>">
            <iconify-icon icon="mdi:account-group" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('sidebar.clients')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Supervisors\DocumentsController::class)): ?>
        <li class="<?php echo e(request()->is('supervisors/documents*') ? 'active-page show open' : ''); ?>">
          <a href="<?php echo e(route('supervisors.documents.index')); ?>" class="<?php echo e(request()->is('supervisors/documents*') ? 'active-page' : ''); ?>">
            <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('sidebar.documents')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Supervisors\MessagesController::class)): ?>
        <li class="<?php echo e(request()->is('supervisors/messages*') ? 'active-page show open' : ''); ?>">
          <a href="<?php echo e(route('supervisors.messages.index')); ?>" class="<?php echo e(request()->is('supervisors/messages*') ? 'active-page' : ''); ?>">
            <iconify-icon icon="bi:chat-dots" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('sidebar.chat')); ?></span>
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</aside><?php /**PATH D:\xampp\htdocs\balantro\resources\views\sidebars\_supervisor_sidebar.blade.php ENDPATH**/ ?>