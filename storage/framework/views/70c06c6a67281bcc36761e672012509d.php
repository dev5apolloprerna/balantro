<div class="flex items-center bg-gray-200 dark:bg-neutral-900 rounded-lg p-1 w-fit">

    <!-- Sales -->
    <a href="<?php echo e(route('transaction_processing.processing_sales')); ?>"
        class="px-3 px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        <?php echo e(request()->routeIs('transaction_processing.processing_sales') 
            ? 'bg-blue-600 text-white text-gray-800 dark:text-white shadow-sm' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-neutral-700'); ?>">
        Sales
    </a>

    <!-- Purchase -->
    <a href="<?php echo e(route('transaction_processing.processing_purchase')); ?>"
        class="px-3 px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        <?php echo e(request()->routeIs('transaction_processing.processing_purchase') 
            ? 'bg-blue-600 text-white text-gray-800 dark:text-white shadow-sm' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-neutral-700'); ?>">
        Purchase
    </a>

    <!-- Bank -->
    <a href="<?php echo e(route('transaction_processing.processing_bank')); ?>"
        class="px-3 px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        <?php echo e(request()->routeIs('transaction_processing.processing_bank') 
            ? 'bg-blue-600 text-white text-gray-800 dark:text-white shadow-sm' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-neutral-700'); ?>">
        Bank
    </a>

    <a href="<?php echo e(route('transaction_processing.processing_credit_note')); ?>"
        class="px-3 px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        <?php echo e(request()->routeIs('transaction_processing.processing_credit_note') 
            ? 'bg-blue-600 text-white text-gray-800 dark:text-white shadow-sm' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-neutral-700'); ?>">
        Credit Note
    </a>
    <a href="<?php echo e(route('transaction_processing.processing_debit_note')); ?>"
        class="px-3 px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        <?php echo e(request()->routeIs('transaction_processing.processing_debit_note') 
            ? 'bg-blue-600 text-white text-gray-800 dark:text-white shadow-sm' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-neutral-700'); ?>">
        Debit Note
    </a>

    <a href="<?php echo e(route('transaction_processing.processing_journal')); ?>"
        class="px-3 px-3 py-1.5 text-xs font-medium rounded-md transition whitespace-nowrap
        <?php echo e(request()->routeIs('transaction_processing.processing_journal') 
            ? 'bg-blue-600 text-white text-gray-800 dark:text-white shadow-sm' 
            : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-neutral-700'); ?>">
        Journal
    </a>

</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\transaction-processing\bulk-upload-tabs.blade.php ENDPATH**/ ?>