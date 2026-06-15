<div id="addManagerModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-neutral-900">
        <div class="flex items-center justify-between border-b pb-3">
            <h2 class="text-lg font-semibold">New Group</h2>
            <button onclick="closeManagerModal()" class="text-neutral-400 hover:text-neutral-600">&times;</button>
        </div>

        <form method="POST" action="<?php echo e(route('groups.store')); ?>" class="mt-4 space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required
                    class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white" />
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeManagerModal()"
                    class="rounded-md border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800">
                    Cancel
                </button>
                <button type="submit"
                    class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\groups\create_modal.blade.php ENDPATH**/ ?>