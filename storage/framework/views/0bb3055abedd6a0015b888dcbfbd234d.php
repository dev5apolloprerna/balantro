<dialog id="modal-client-form" class="rounded-2xl backdrop:bg-black/50 dark:backdrop:bg-white/30 w-full max-w-3xl">
    <form method="dialog" class="w-full">
        <div class="rounded-2xl bg-white dark:bg-neutral-800 shadow-xl">
            <div class="flex items-center justify-between border-b px-2 py-1 dark:border-neutral-700">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Client</h3>

                <button type="button"
                    class="h-8 w-8 grid place-items-center rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-700"
                    data-modal-close>✕</button>
            </div>
            <div class="grid gap-2 p-1">
                <input type="hidden" id="cf-client-id">
                
                <input type="hidden" name="fcm_token" id="c_fcm_token">
                <input type="hidden" name="device_type" id="c_device_type">
                <input type="hidden" name="browser_name" id="c_browser_name">
                <input type="hidden" name="os_name" id="c_os_name">
                
                <div>
                    <label for="cf-business-type"
                        class="block text-sm font-semibold mb-1"><?php echo e(__('Business Type')); ?></label>
                    <select id="cf-business-type" class="w-full px-1 py-1 border rounded-lg bg-white text-neutral-900 dark:bg-neutral-900 dark:text-white dark:border-neutral-700">
                        <option value=""><?php echo e(__('Select...')); ?></option>
                        <?php $__currentLoopData = \App\Models\Profile::BUSINESS_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div>
                        <label for="cf-name" class="block text-sm font-semibold mb-1"><?php echo e(__('Name')); ?> <span
                                class="text-rose-600">*</span></label>
                        <input id="cf-name" type="text" class="w-full px-1 py-1 border rounded-lg">
                        <div id="cf-name-error" class="text-sm text-rose-600 mt-1"></div>
                    </div>

                    <div>
                        <label for="cf-email" class="block text-sm font-semibold mb-1"><?php echo e(__('Email')); ?> <span
                                class="text-rose-600">*</span></label>
                        <input id="cf-email" type="email" class="w-full px-1 py-1 border rounded-lg">
                        <div id="cf-email-error" class="text-sm text-rose-600 mt-1"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div>
                        <label for="cf-mobile" class="block text-sm font-semibold mb-1"><?php echo e(__('Mobile No')); ?></label>
                        <input id="cf-mobile" type="text" class="w-full px-1 py-1 border rounded-lg">
                        <div id="cf-mobile-error" class="text-sm text-rose-600 mt-1"></div>
                    </div>
                    <div>
                        <label for="cf-whatsapp"
                            class="block text-sm font-semibold mb-1"><?php echo e(__('WhatsApp No')); ?></label>
                        <input id="cf-whatsapp" type="text" class="w-full px-1 py-1 border rounded-lg">
                        <div id="cf-whatsapp-error" class="text-sm text-rose-600 mt-1"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div>
                        <label for="cf-pan" class="block text-sm font-semibold mb-1"><?php echo e(__('PAN No')); ?></label>
                        <input id="cf-pan" type="text" class="w-full px-1 py-1 border rounded-lg">
                    </div>
                    <div>
                        <label for="cf-gst" class="block text-sm font-semibold mb-1"><?php echo e(__('GST No')); ?></label>
                        <input id="cf-gst" type="text" class="w-full px-1 py-1 border rounded-lg">
                    </div>
                </div>

                <div>
                    <label for="cf-guid" class="block text-sm font-semibold mb-1"><?php echo e(__('GUID')); ?> <span
                            class="text-rose-600">*</span></label>
                    <input id="cf-guid" type="text" class="w-full px-1 py-1 border rounded-lg">
                    <div id="cf-guid-error" class="text-sm text-rose-600 mt-1"></div>
                </div>

                <div>
                    <label for="cf-address" class="block text-sm font-semibold mb-1"><?php echo e(__('Address')); ?></label>
                    <textarea id="cf-address" rows="2" class="w-full px-1 py-1 border rounded-lg"></textarea>
                </div>

                <div>
                    <label for="cf-isStockManagement"
                        class="block text-sm font-semibold mb-1"><?php echo e(__('Is Stock Mangement?')); ?></label>
                    <select id="cf-isStockManagement" class="w-full px-1 py-1 border rounded-lg bg-white text-neutral-900 dark:bg-neutral-900 dark:text-white dark:border-neutral-700">
                        <option value=""><?php echo e(__('Select...')); ?></option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                        
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 border-t px-1 py-1 dark:border-neutral-700">
                <button type="button"
                    class="rounded-lg border border-rose-600 px-2 py-1 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-600/20"
                    data-modal-close><?php echo e(__('Close')); ?></button>
                <button type="button" id="cf-save"
                    class="rounded-lg bg-primary-600 px-2 py-1 font-semibold text-white hover:bg-primary-700"><?php echo e(__('Save')); ?></button>
            </div>
        </div>
    </form>
</dialog>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/clients/modals/create.blade.php ENDPATH**/ ?>