<div id="client-modal" data-client-modal-target="modal" data-action="keydown@window->client-modal#handleKeydown click->client-modal#closeBackground" 
    class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
    <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 dark:border-gray-600 rounded-t">
            <span class="text-xl font-semibold text-gray-900 dark:text-white"><?php echo app('translator')->get('client_modal.title'); ?></span>
            <button type="button" data-action="client-modal#close" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
        <div class="p-6 space-y-6">
            <form action="<?php echo e(route('admin.clients.store')); ?>" method="POST" class="space-y-6">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-gray-700 dark:text-gray-300 mb-2 required">
                            <?php echo app('translator')->get('client_modal.form.client_name.label'); ?>
                        </label>
                        <input type="text" name="name" id="name" placeholder="<?php echo app('translator')->get('client_modal.form.client_name.placeholder'); ?>" 
                               class="form-control border border-neutral-300 bg-custom-input dark:bg-dark-2 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="form-error text-sm text-red-600 dark:text-red-400 mt-1"><?php echo app('translator')->get('client_modal.form.client_name.label'); ?> <?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="email" class="block text-gray-700 dark:text-gray-300 mb-2 required">
                            <?php echo app('translator')->get('client_modal.form.email.label'); ?>
                        </label>
                        <input type="email" name="email" id="email" placeholder="<?php echo app('translator')->get('client_modal.form.email.placeholder'); ?>" 
                               class="form-control border border-neutral-300 bg-custom-input dark:bg-dark-2 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="form-error text-sm text-red-600 dark:text-red-400 mt-1"><?php echo app('translator')->get('client_modal.form.email.label'); ?> <?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="business_type" class="block text-gray-700 dark:text-gray-300 mb-2">
                            <?php echo app('translator')->get('client_modal.form.business_type.label'); ?>
                        </label>
                        <select name="business_type" id="business_type" class="form-control border border-neutral-300 bg-custom-input dark:bg-dark-2 <?php $__errorArgs = ['profile.business_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value=""><?php echo app('translator')->get('client_modal.form.business_type.prompt'); ?></option>
                            <?php $__currentLoopData = $businessTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php if(old('profile.business_type') == $key): ?> selected <?php endif; ?>>
                                    <?php echo e($value); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['profile.business_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="form-error text-sm text-red-600 dark:text-red-400 mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="pan_no" class="block text-gray-700 dark:text-gray-300 mb-2">
                            <?php echo app('translator')->get('client_modal.form.pan_no.label'); ?>
                        </label>
                        <input type="text" name="profile[pan_no]" id="pan_no" placeholder="<?php echo app('translator')->get('client_modal.form.pan_no.placeholder'); ?>" 
                               class="form-control border border-neutral-300 bg-custom-input dark:bg-dark-2 <?php $__errorArgs = ['profile.pan_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['profile.pan_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="form-error text-sm text-red-600 dark:text-red-400 mt-1"><?php echo app('translator')->get('client_modal.form.pan_no.label'); ?> <?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="gst_no" class="block text-gray-700 dark:text-gray-300 mb-2">
                            <?php echo app('translator')->get('client_modal.form.gst_no.label'); ?>
                        </label>
                        <input type="text" name="profile[gst_no]" id="gst_no" placeholder="<?php echo app('translator')->get('client_modal.form.gst_no.placeholder'); ?>" 
                               class="form-control border border-neutral-300 bg-custom-input dark:bg-dark-2 <?php $__errorArgs = ['profile.gst_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['profile.gst_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="form-error text-sm text-red-600 dark:text-red-400 mt-1"><?php echo app('translator')->get('client_modal.form.gst_no.label'); ?> <?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="mobile_no" class="block text-gray-700 dark:text-gray-300 mb-2">
                            <?php echo app('translator')->get('client_modal.form.mobile_no.label'); ?>
                        </label>
                        <input type="text" name="profile[mobile_no]" id="mobile_no" placeholder="<?php echo app('translator')->get('client_modal.form.mobile_no.placeholder'); ?>" 
                               class="form-control border border-neutral-300 bg-custom-input dark:bg-dark-2 <?php $__errorArgs = ['profile.mobile_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['profile.mobile_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="form-error text-sm text-red-600 dark:text-red-400 mt-1"><?php echo app('translator')->get('client_modal.form.mobile_no.label'); ?> <?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-gray-700 dark:text-gray-300 mb-2">
                        <?php echo app('translator')->get('client_modal.form.address.label'); ?>
                    </label>
                    <textarea name="profile[address]" id="address" placeholder="<?php echo app('translator')->get('client_modal.form.address.placeholder'); ?>" 
                              class="form-control border border-neutral-300 bg-custom-input dark:bg-dark-2 <?php $__errorArgs = ['profile.address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"></textarea>
                    <?php $__errorArgs = ['profile.address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="form-error text-sm text-red-600 dark:text-red-400 mt-1"><?php echo app('translator')->get('client_modal.form.address.label'); ?> <?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                        
                <div class="flex justify-end space-x-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded cursor-pointer">
                        <?php echo app('translator')->get('client_modal.buttons.submit'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\users\client_modal.blade.php ENDPATH**/ ?>