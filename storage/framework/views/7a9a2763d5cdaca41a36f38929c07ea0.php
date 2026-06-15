
<dialog id="modal-assign-groups" class="rounded-2xl backdrop:bg-black/50 dark:backdrop:bg-white/30 w-full max-w-xl">
    <form method="dialog" class="w-full">
        <div class="rounded-2xl bg-white dark:bg-neutral-800 shadow-xl">
            <div class="flex items-center justify-between border-b px-5 py-3 dark:border-neutral-700">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white"><?php echo e(__('Assign Groups')); ?></h3>
                <button type="button"
                    class="h-8 w-8 grid place-items-center rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-700"
                    data-modal-close>✕</button>
            </div>

            <div class="space-y-3 p-5">
                <input type="hidden" id="ag-client-id">
                <div id="ag-groups-list" class="space-y-2">
                    
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t px-5 py-3 dark:border-neutral-700">
                <button type="button"
                    class="rounded-lg border border-rose-600 px-4 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-600/20"
                    data-modal-close><?php echo e(__('Close')); ?></button>
                <button type="button" id="ag-save"
                    class="rounded-lg bg-primary-600 px-4 py-2 font-semibold text-white hover:bg-primary-700"><?php echo e(__('Save')); ?></button>
            </div>
        </div>
    </form>
</dialog>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\clients\modals\assign_groups_plain.blade.php ENDPATH**/ ?>