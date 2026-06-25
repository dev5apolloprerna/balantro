<div class="mt-6 flex items-center gap-4 border-b border-gray-200 dark:border-gray-700">
    <a href="<?php echo e(route('home')); ?>?tab=financial"
        class="px-4 py-2 -mb-px text-sm font-medium border-b-2 transition
            <?php echo e((request()->routeIs('home') && request()->get('tab', 'financial') == 'financial') ||
            request()->routeIs('reports.pnl') ||
            request()->routeIs('reports.balanceSheet') ||
            request()->routeIs('reports.ledger') ||
            request()->routeIs('reports.voucherHistory')
                ? 'border-blue-600 text-blue-600 dark:text-blue-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'); ?>">
        Financial Dashboard
    </a>

    <a href="<?php echo e(route('home')); ?>?tab=documents"
        class="px-4 py-2 -mb-px text-sm font-medium border-b-2 transition
            <?php echo e(request()->routeIs('home') && request()->get('tab') == 'documents'
                ? 'border-blue-600 text-blue-600 dark:text-blue-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'); ?>">
        Document Dashboard
    </a>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views/client_dashboard/topmenu.blade.php ENDPATH**/ ?>