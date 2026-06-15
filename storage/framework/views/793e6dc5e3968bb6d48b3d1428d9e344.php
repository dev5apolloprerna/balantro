

<?php $__env->startSection('content'); ?>

<div class="container mx-auto">

    <div class="rounded-2xl  p-6 shadow-sm
            border border-cyan-400/10 bg-white/5 backdrop-blur-xl">

        <h2 class="mb-6 text-xl font-bold text-neutral-900 dark:text-white">
            GST Settings
        </h2>

        <form action="<?php echo e(route('gst.setting.update')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                
                <div>
                    <label class="mb-2 block font-medium text-neutral-700 dark:text-neutral-200">
                        CGST Ledger
                    </label>

                    <select name="cgst_ledger_id"
                        class="w-full rounded-lg border border-neutral-300
                            bg-white px-4 py-3 text-neutral-900
                            focus:border-primary-500 focus:ring-1 focus:ring-primary-500
                            dark:border-neutral-600
                            dark:bg-neutral-800
                            dark:text-white">
                        <option value="">Select CGST Ledger</option>

                        <?php $__currentLoopData = $cgstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ledger->iLedgerId); ?>" <?php echo e(isset($setting) && $setting->CGSTLedgerId == $ledger->iLedgerId ? 'selected' : ''); ?>>
                                <?php echo e($ledger->strCustomerName); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div>
                    <label class="mb-2 block font-medium text-neutral-700 dark:text-neutral-200">
                        SGST Ledger
                    </label>

                    <select name="sgst_ledger_id"
                        class="w-full rounded-lg border border-neutral-300
                            bg-white px-4 py-3 text-neutral-900
                            focus:border-primary-500 focus:ring-1 focus:ring-primary-500
                            dark:border-neutral-600
                            dark:bg-neutral-800
                            dark:text-white">
                        <option value="">Select SGST Ledger</option>

                        <?php $__currentLoopData = $sgstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ledger->iLedgerId); ?>" <?php echo e(isset($setting) && $setting->SGSTLedgerId == $ledger->iLedgerId ? 'selected' : ''); ?>>
                                <?php echo e($ledger->strCustomerName); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div>
                    <label class="mb-2 block font-medium text-neutral-700 dark:text-neutral-200">
                        IGST Ledger
                    </label>

                    <select name="igst_ledger_id"
                        class="w-full rounded-lg border border-neutral-300
                            bg-white px-4 py-3 text-neutral-900
                            focus:border-primary-500 focus:ring-1 focus:ring-primary-500
                            dark:border-neutral-600
                            dark:bg-neutral-800
                            dark:text-white">
                        <option value="">Select IGST Ledger</option>

                        <?php $__currentLoopData = $igstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ledger->iLedgerId); ?>" <?php echo e(isset($setting) && $setting->IGSTLedgerId == $ledger->iLedgerId ? 'selected' : ''); ?>>
                                <?php echo e($ledger->strCustomerName); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
               
            </div>

            <div class="mt-2">
                <label class="inline-flex items-center">
                    <input type="checkbox"
                        name="is_active"
                        value="1"
                        <?php echo e(isset($setting) && $setting->IsActive ? 'checked' : ''); ?>

                        class="h-4 w-4 rounded border-neutral-300
                            text-primary-600
                            focus:ring-primary-500">

                    <span class="ml-2 text-neutral-700 dark:text-neutral-200">
                        Enable GST
                    </span>
                </label>
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="rounded-md border border-gray-700 text-black dark:text-white h-9.5 px-4 py-2 text-sm transition duration-1000 ease-in-out
                                    transition-property: all;
                                    hover:border-[#22d3ee]
                                    hover:shadow-[0_0_15px_#22d3ee]
                                    hover:scale-105
                                    hover:-translate-y-1">
                    Save
                </button>
            </div>

        </form>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\settings\gst\index.blade.php ENDPATH**/ ?>