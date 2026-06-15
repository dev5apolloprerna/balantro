<div id="users-edit-modal" data-users-edit-target="modal"
    class="hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full overflow-x-hidden overflow-y-auto md:inset-0 h-screen bg-black/50 dark:bg-white/30">
    <div
        class="bg-white dark:bg-dark-2 rounded-2xl max-w-[40rem] w-full shadow-lg mx-4 md:mx-0 max-h-[90vh] md:max-h-full overflow-y-auto">
        <!-- Modal Header -->
        <div class="py-4 px-6 border-b border-neutral-200 dark:border-neutral-600 flex items-center justify-between">
            <h6 class="font-semibold text-gray-900 dark:text-white"><?php echo e(__('common.edit.edit')); ?></h6>
            <button type="button" data-action="users-edit#cancel"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-4 md:p-5 space-y-4">
            <form id="users-edit-form" data-users-edit-target="form" data-action="submit->users-edit#submit">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                    <label for="users-edit-name"
                        class="block text-sm font-semibold text-neutral-600 dark:text-neutral-200 mb-2">
                        <?php echo e(__('common.edit.name_label')); ?> <span class="text-danger-600">*</span>
                    </label>
                    <input type="text" name="name" id="users-edit-name"
                        placeholder="<?php echo e(__('common.edit.name_placeholder')); ?>" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-500 rounded-lg focus:outline-none focus:ring-0 focus:ring-blue-500 focus:border-blue-500 dark:text-white placeholder-gray-500"
                        data-users-edit-target="nameField">
                    <p id="users-edit-name-error" data-users-edit-target="nameError"
                        class="text-danger-600 dark:text-danger-400 form-error mt-1 hidden">
                    </p>
                </div>
                <div class="mb-4">
                    <label for="users-edit-email"
                        class="block text-sm font-semibold text-neutral-600 dark:text-neutral-200 mb-2">
                        <?php echo e(__('common.edit.email_label')); ?> <span class="text-danger-600">*</span>
                    </label>
                    <input type="email" name="email" id="users-edit-email"
                        placeholder="<?php echo e(__('common.edit.email_placeholder')); ?>" readonly
                        class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-md text-gray-600 dark:text-gray-300 rounded-xl focus:outline-none py-2 px-4 cursor-not-allowed"
                        data-users-edit-target="emailField">
                </div>
                <!-- Modal Footer -->
                <div
                    class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-600 rounded-b">
                    <button type="button"
                        class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-danger-600 bg-hover-danger-200 !text-danger-600 text-center"
                        data-action="click->users-edit#cancel">
                        <?php echo e(__('common.edit.cancel')); ?>

                    </button>
                    <button type="submit"
                        class="w-24 py-2 px-4 rounded-lg cursor-pointer border border-primary-600 bg-primary-600 hover:bg-primary-700 text-white text-center"
                        data-users-edit-target="submitButton">
                        <?php echo e(__('common.edit.save_button')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\shared\users_edit_modal.blade.php ENDPATH**/ ?>