<aside class="sidebar">
  <button type="button" class="sidebar-close-btn !mt-4">
    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
  </button>
  <div>
    <a href="<?php echo e(route('client.dashboard')); ?>" class="sidebar-logo">
      <img src="<?php echo e(asset('images/light-logo.svg')); ?>" alt="light-logo" class="light-logo">
      <img src="<?php echo e(asset('images/dark-logo.svg')); ?>" alt="dark-logo" class="dark-logo">
      <img src="<?php echo e(asset('images/small-logo.svg')); ?>" alt="small-logo" class="logo-icon">
    </a>
  </div>
  <div class="sidebar-menu-area">
    <ul class="sidebar-menu" id="sidebar-menu">
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\ClientDashboardController::class)): ?>
        <li>
          <a href="<?php echo e(route('client.dashboard')); ?>">
            <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('client.sidebar.menu.dashboard')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\DocumentsController::class)): ?>
        <li class="<?php echo e(request()->is('documents*') ? 'active-page show open' : ''); ?>">
          <a href="<?php echo e(route('documents.index')); ?>" class="<?php echo e(request()->is('documents*') ? 'active-page' : ''); ?>">
            <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>
            <span><?php echo e(__('client.sidebar.menu.documents')); ?></span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Http\Controllers\Client\MessagesController::class)): ?>
        <?php if(auth()->user()->data_entry_operators->count() > 0): ?>
          <li class="<?php echo e(request()->is('client/messages*') ? 'active-page show open' : ''); ?>">
            <a href="<?php echo e(route('client.messages.index')); ?>" class="<?php echo e(request()->is('client/messages*') ? 'active-page' : ''); ?>">
              <iconify-icon icon="bi:chat-dots" class="menu-icon"></iconify-icon>
              <span><?php echo e(__('sidebar.chat')); ?></span>
            </a>
          </li>
        <?php endif; ?>
      <?php endif; ?>
    </ul>
  </div>
</aside><?php /**PATH D:\xampp\htdocs\balantro\resources\views\sidebars\_client_sidebar.blade.php ENDPATH**/ ?>