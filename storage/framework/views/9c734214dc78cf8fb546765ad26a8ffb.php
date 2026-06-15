<ul class="space-y-1 px-2">
    <li>
        <a href="<?php echo e(route('home')); ?>" title="Dashboard"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?php echo e(Route::is('home') ? 'active bg-gray-200 dark:bg-gray-600' : ''); ?>">
            <i class="fas fa-chart-bar text-xl mr-3"></i>
            <span class="nav-text flex-1">Dashboard</span>
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
        <a href="<?php echo e(route('deo.messages.index')); ?>" title="Chat"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?php echo e(Route::is('deo.*') ? 'active bg-gray-200 dark:bg-gray-600' : ''); ?>">
            <i class="fas fa-comment-dots menu-icon  text-xl mr-3"></i>
            <span class="nav-text flex-1">Chat</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('data_entry_operators.bulkuploadsales')); ?>" title="Financial Management"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?php echo e((Route::is('data_entry_operators.*') || Route::is('sales.*') || Route::is('purchase.*') || Route::is('bank.*') || Route::is('cn.*') || Route::is('dn.*') || Route::is('journal.*')) ? 'active bg-gray-200 dark:bg-gray-600' : ''); ?>">
            <i class="fas fa-upload menu-icon  text-xl mr-3"></i>
            <span class="nav-text flex-1">Financial Management</span>
        </a>
    </li>
    <li>
        <a href="<?php echo e(route('transaction_processing.processing_sales')); ?>" title="Transaction Management"
            class="nav-item flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 <?php echo e(Route::is('transaction_processing.*') ? 'active bg-gray-200 dark:bg-gray-600' : ''); ?>">
            <i class="fas fa-exchange menu-icon  text-xl mr-3"></i>
            <span class="nav-text flex-1">Transaction Management
                
            </span>
        </a>
    </li>
</ul>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\navigations\data_entry_operator_nav.blade.php ENDPATH**/ ?>