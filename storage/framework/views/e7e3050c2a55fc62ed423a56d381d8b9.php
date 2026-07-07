<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div class="mt-4 sm:ml-auto flex flex-wrap items-center gap-2" role="tablist">
        <a href="<?php echo e(route('clients.dashboard', $guid ?? '')); ?>" style="padding-top: 0.40rem;"
            class="h-9 px-3 text-sm rounded-md border transition
           <?php echo e(request()->routeIs('clients.dashboard')
               ? 'bg-blue-600 text-white border-blue-600'
               : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50'); ?>">
            Financial Dashboard
        </a>
		<a href="<?php echo e(route('clients.documents.dashboard', $guid ?? '')); ?>" style="padding-top: 0.40rem;"
            class="h-9 px-3 text-sm rounded-md border transition
           <?php echo e(request()->routeIs('clients.documents.dashboard')
               ? 'bg-blue-600 text-white border-blue-600'
               : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50'); ?>">
            Document Dashboard
        </a>
		
        <a href="<?php echo e(route('clients.reports.balanceSheet', $guid ?? '')); ?>" style="padding-top: 0.40rem;"
            class="h-9 px-3 text-sm rounded-md border transition
           <?php echo e(request()->routeIs('clients.reports.balanceSheet')
               ? 'bg-blue-600 text-white border-blue-600'
               : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50'); ?>">
            Balance Sheet
        </a>
        <a href="<?php echo e(route('clients.reports.pnl', $guid ?? '')); ?>" style="padding-top: 0.40rem;"
            class="h-9 px-3 text-sm rounded-md border transition
           <?php echo e(request()->routeIs('clients.reports.pnl')
               ? 'bg-blue-600 text-white border-blue-600'
               : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50'); ?>">
            Profit & Loss
        </a>

        <a href="<?php echo e(route('clients.reports.ledger', $guid ?? '')); ?>" style="padding-top: 0.40rem;"
            class="h-9 px-3 text-sm rounded-md border transition
           <?php echo e(request()->routeIs('clients.reports.ledger')
               ? 'bg-blue-600 text-white border-blue-600'
               : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50'); ?>">
            All Ledger
        </a>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/clients/reports/tabmanu.blade.php ENDPATH**/ ?>