<div class="accordion-card rounded-xl border border-cyan-500/20 overflow-hidden">
    <div class="accordion-btn cursor-pointer bg-cyan-600 text-white px-4 py-3 flex justify-between items-center">
        <span class="font-semibold">
            <?php echo e($title); ?>

        </span>
        <span>▼</span>
    </div>
    <div class="accordion-body hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-700 text-white">
                    <tr>
                        <th class="p-3 text-left">Ledger</th>
                        <th class="p-3 text-center">CGST</th>
                        <th class="p-3 text-center">SGST</th>
                        <th class="p-3 text-center">IGST</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b border-gray-700 hover:bg-cyan-500/5">
                        <td class="p-3 text-neutral-900 dark:text-white">
                            <?php echo e($ledger->strCustomerName); ?>

                            <input type="hidden"
                                name="ledger_ids[]"
                                value="<?php echo e($ledger->iLedgerId); ?>">
                        </td>
                        <td class="p-2">
                            <select
                                name="ledger_cgst[<?php echo e($ledger->iLedgerId); ?>]"
                                class="w-full rounded border border-gray-600 bg-white dark:bg-neutral-800 dark:text-white">
                                <option value="">Select</option>
                                <?php $__currentLoopData = $cgstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($gst->iLedgerId); ?>" <?php echo e($ledger->CGSTLedgerId == $gst->iLedgerId ? 'selected' : ''); ?>>
                                        <?php echo e($gst->strCustomerName); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td class="p-2">
                            <select
                                name="ledger_sgst[<?php echo e($ledger->iLedgerId); ?>]"
                                class="w-full rounded border border-gray-600 bg-white dark:bg-neutral-800 dark:text-white">
                                <option value="">Select</option>
                                <?php $__currentLoopData = $sgstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($gst->iLedgerId); ?>" <?php echo e($ledger->SGSTLedgerId == $gst->iLedgerId ? 'selected' : ''); ?>>
                                        <?php echo e($gst->strCustomerName); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td class="p-2">
                            <select
                                name="ledger_igst[<?php echo e($ledger->iLedgerId); ?>]"
                                class="w-full rounded border border-gray-600 bg-white dark:bg-neutral-800 dark:text-white">
                                <option value="">Select</option>
                                <?php $__currentLoopData = $igstLedgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($gst->iLedgerId); ?>" <?php echo e($ledger->IGSTLedgerId == $gst->iLedgerId ? 'selected' : ''); ?>>
                                        <?php echo e($gst->strCustomerName); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-400">
                            No Ledger Found
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\balantro\resources\views\admin\clients\settings\gst\partials\card.blade.php ENDPATH**/ ?>