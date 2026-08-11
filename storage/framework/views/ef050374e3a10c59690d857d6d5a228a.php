<?php
$isEdit = ($mode ?? 'create') === 'edit';
$fieldPrefix = $isEdit ? 'e' : 'c';
?>

<div id="<?php echo e($modalId); ?>"
    class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
    role="dialog" aria-modal="true" aria-labelledby="<?php echo e($modalId); ?>-title">
    <button type="button" class="absolute inset-0 cursor-default" aria-label="Close modal"
        onclick="<?php echo e($closeAction); ?>"></button>

    <div class="balantro-modal-panel relative w-full max-w-md rounded-2xl bg-white text-slate-900 shadow-xl dark:bg-slate-900 dark:text-white">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-700">
            <h2 id="<?php echo e($modalId); ?>-title" class="text-lg font-semibold"><?php echo e($title); ?></h2>
            <button type="button" onclick="<?php echo e($closeAction); ?>"
                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white"
                aria-label="Close modal">&times;</button>
        </div>

        <form id="<?php echo e($formId); ?>" method="POST" <?php if(!empty($action)): ?> action="<?php echo e($action); ?>" <?php endif; ?>
            class="space-y-4 px-6 py-5">
            <?php echo csrf_field(); ?>
            <?php if($isEdit): ?>
            <?php echo method_field($method ?? 'PUT'); ?>
            <?php endif; ?>

            <input type="hidden" name="fcm_token" id="<?php echo e($fieldPrefix); ?>_fcm_token">
            <input type="hidden" name="device_type" id="<?php echo e($fieldPrefix); ?>_device_type">
            <input type="hidden" name="browser_name" id="<?php echo e($fieldPrefix); ?>_browser_name">
            <input type="hidden" name="os_name" id="<?php echo e($fieldPrefix); ?>_os_name">

            <div>
                <label for="<?php echo e($nameId); ?>" class="mb-1 block text-sm font-medium">
                    Name <span class="text-red-500">*</span>
                </label>
                <input id="<?php echo e($nameId); ?>" name="name" type="text" required maxlength="255"
                    class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </div>

            <div>
                <label for="<?php echo e($emailId); ?>" class="mb-1 block text-sm font-medium">
                    Email <span class="text-red-500">*</span>
                </label>
                <input id="<?php echo e($emailId); ?>" name="email" type="email" required maxlength="255"
                    pattern="[^\s@]+@[^\s@]+\.[A-Za-z]{2,}"
                    title="Enter an email address with a valid domain (for example, name@company.com)."
                    class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-200 pt-4 dark:border-slate-700">
                <button type="button" onclick="<?php echo e($closeAction); ?>"
                    class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    Cancel
                </button>
                <button type="submit"
                    class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/shared/user_form_modal.blade.php ENDPATH**/ ?>