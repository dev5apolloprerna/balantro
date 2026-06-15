<div class="flex items-center gap-1 border border-gray-200 dark:border-neutral-600 rounded-md px-1 py-1 bg-white dark:bg-neutral-800">

    <a href="<?php echo e(route('data_entry_operators.bulkuploadsales')); ?>"
        class="px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        <?php echo e(request()->routeIs('data_entry_operators.bulkuploadsales') 
            ? 'bg-blue-600 text-white' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700'); ?>">
        Sales
    </a>
    <a href="<?php echo e(route('data_entry_operators.bulkuploadpurchase')); ?>"
        class="px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        <?php echo e(request()->routeIs('data_entry_operators.bulkuploadpurchase') 
            ? 'bg-blue-600 text-white' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700'); ?>">
        Purchase
    </a>
    <a href="<?php echo e(route('data_entry_operators.bulkuploadbankstatement')); ?>"
        class="px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        <?php echo e(request()->routeIs('data_entry_operators.bulkuploadbankstatement') 
            ? 'bg-blue-600 text-white' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700'); ?>">
        Bank
    </a>
    <a href="<?php echo e(route('cn.index')); ?>"
        class="px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        <?php echo e(request()->routeIs('cn.index') 
            ? 'bg-blue-600 text-white' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700'); ?>">
        Credit Note
    </a>
    <a href="<?php echo e(route('dn.index')); ?>"
        class="px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        <?php echo e(request()->routeIs('dn.index') 
            ? 'bg-blue-600 text-white' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700'); ?>">
        Debit Note
    </a>
    <a href="<?php echo e(route('journal.index')); ?>"
        class="px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        <?php echo e(request()->routeIs('journal.index') 
            ? 'bg-blue-600 text-white' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700'); ?>">
        Journal
    </a>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\bulkupload\bulk-upload-tabs.blade.php ENDPATH**/ ?>