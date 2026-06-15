<ul class="space-y-1 px-2">
    <li>
        <a href="<?php echo e(route('home')); ?>" title="Dashboard"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?php echo e(Route::is('home') ? 'active bg-gray-200 dark:bg-gray-600' : ''); ?>">
            <i class="fas fa-chart-bar text-xl mr-3"></i>
            <span class="nav-text flex-1">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('data_entry_operators.index')); ?>" title="Data Entry Operators"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?php echo e(Route::is('data_entry_operators.*') ? 'active bg-gray-200 dark:bg-gray-600' : ''); ?>">
            <i class="fas fa-keyboard menu-icon text-xl mr-3"></i>
            <span class="nav-text flex-1">Data Entry Operators</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('clients.index')); ?>" title="Clients"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?php echo e(Route::is('clients.*') ? 'active bg-gray-200 dark:bg-gray-600' : ''); ?>">
            <i class="fa-solid fa-users menu-icon  text-xl mr-3"></i>
            <span class="nav-text flex-1">Clients</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('documents.index')); ?>" title="Documents"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?php echo e(Route::is('documents.*') ? 'active bg-gray-200 dark:bg-gray-600' : ''); ?>">
            <i class="fa-solid fa-file-lines menu-icon text-xl mr-3"></i>
            <span class="nav-text flex-1">Documents</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('supervisor.messages.index')); ?>" title="Chat"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?php echo e(Route::is('supervisor.*') ? 'active bg-gray-200 dark:bg-gray-600' : ''); ?>">
            <i class="fas fa-comment-dots menu-icon  text-xl mr-3"></i>
            <span class="nav-text flex-1">Chat</span>
        </a>
    </li>
</ul>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\navigations\supervisor_nav.blade.php ENDPATH**/ ?>