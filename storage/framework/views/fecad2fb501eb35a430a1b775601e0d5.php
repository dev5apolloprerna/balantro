
<dialog id="modal-assign-users" class="rounded-2xl backdrop:bg-black/50 dark:backdrop:bg-white/30 w-full max-w-xl">
    <form method="dialog" class="w-full">
        <div class="rounded-2xl bg-white dark:bg-neutral-800 shadow-xl">
            <div class="flex items-center justify-between border-b px-5 py-3 dark:border-neutral-700">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white"><?php echo e(__('Assign Users')); ?></h3>
                <button type="button"
                    class="h-8 w-8 grid place-items-center rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-700"
                    data-modal-close>
                    ✕
                </button>
            </div>

            <div class="space-y-4 p-5">
                <input type="hidden" id="au-client-id">

                <div>
                    <label class="mb-1 block text-sm font-medium"><?php echo e(__('Manager')); ?></label>
                    <select id="au-manager"
                        class="w-full rounded-lg border border-neutral-300 px-3 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value=""><?php echo e(__('Select Manager')); ?></option>
                        <?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m->id); ?>"><?php echo e($m->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium"><?php echo e(__('Supervisor')); ?></label>
                    <select id="au-supervisor"
                        class="w-full rounded-lg border border-neutral-300 px-3 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white"
                        disabled>
                        <option value=""><?php echo e(__('Select Supervisor (choose manager first)')); ?></option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium"><?php echo e(__('Data Entry Operator')); ?></label>
                    <select id="au-deo"
                        class="w-full rounded-lg border border-neutral-300 px-3 py-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white"
                        disabled>
                        <option value=""><?php echo e(__('Select Data Entry Operator (choose supervisor first)')); ?></option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t px-5 py-3 dark:border-neutral-700">
                <button type="button"
                    class="rounded-lg border border-rose-600 px-4 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-600/20"
                    data-modal-close>
                    <?php echo e(__('Close')); ?>

                </button>
                <button type="button" id="au-save"
                    class="rounded-lg bg-primary-600 px-4 py-2 font-semibold text-white hover:bg-primary-700">
                    <?php echo e(__('Assign')); ?>

                </button>
            </div>
        </div>
    </form>
</dialog>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\clients\modals\assign_users_plain.blade.php ENDPATH**/ ?>