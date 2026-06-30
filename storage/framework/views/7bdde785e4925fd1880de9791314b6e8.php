

<?php $__env->startSection('content'); ?>

<div class="px-4 py-2 text-black dark:text-white text-sm">

    
    <div class="flex justify-between items-start border-b border-gray-500 pb-2">

        <div>

            <div class="text-2xl font-bold">
                <?php echo e(strtoupper($header->vchType)); ?>

            </div>

            <div class="mt-2 space-y-1">

                <div>
                    <span class="text-black dark:text-white">
                        Voucher No :
                    </span>

                    <span class="ml-2 font-semibold">
                        <?php echo e($header->vchNo); ?>

                    </span>
                </div>

                <div>
                    <span class="text-black dark:text-white">
                        Date :
                    </span>

                    <span class="ml-2">
                        <?php echo e(date('d-M-y', strtotime($header->strVchDate))); ?>

                    </span>
                </div>

            </div>

        </div>

        <div class="flex items-center gap-2">

            <!-- [
                'vchNo' => $header->vchNo,
                'vchType' => $header->vchType
            ] -->
            
            <a href="<?php echo e(route('reports.voucher.export.pdf', 
                [
                    'strGUID' => urlencode($header->strGUID),
                    'vchType' => urlencode($header->vchType)
                ]
                )); ?>"
            title="Export PDF"
            class="group btn inline-flex items-center justify-center
                    w-10 h-10 rounded-md border border-gray-700
                    text-black dark:text-white
                    hover:border-[#ef4444]
                    hover:shadow-[0_0_15px_#ef4444]
                    hover:scale-105
                    hover:-translate-y-1"
            style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">

                <i class="fa-solid fa-file-pdf"></i>

            </a>

            
            <a href="<?php echo e(route('reports.voucher.export.excel',[
                    'strGUID' => urlencode($header->strGUID),
                    'vchType' => urlencode($header->vchType)
                ])); ?>"
            title="Export Excel"
            class="group btn inline-flex items-center justify-center
                    w-10 h-10 rounded-md border border-gray-700
                    text-black dark:text-white
                    hover:border-[#22c55e]
                    hover:shadow-[0_0_15px_#22c55e]
                    hover:scale-105
                    hover:-translate-y-1"
            style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">

                <i class="fa-solid fa-file-excel"></i>

            </a>

            
            <a href="<?php echo e(url()->previous()); ?>"
            title="Go Back"
            class="group btn inline-block relative
                    text-black dark:text-white
                    px-4 py-2 text-sm rounded-md
                    border border-gray-700
                    hover:border-[#f472b6]
                    hover:shadow-[0_0_15px_#f472b6]
                    hover:scale-105
                    hover:-translate-y-1"
            style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">

                <i class="fa-solid fa-arrow-left mr-1"></i>

            </a>

        </div>

    </div>

    
    <?php
        $partyLedger = $header; // $voucher->firstWhere('CRAmount', '>', 0);
    ?>

    <div class="mt-4 border-b border-gray-600 pb-2">

        <div class="flex">

            <div class="w-48 text-black dark:text-white">
                Party A/c Name
            </div>

            <div class="font-semibold">
                <?php echo e($partyLedger->trnAccount ?? ''); ?>

            </div>

        </div>

    </div>

    
    <div class="mt-4">

        
        <div class="flex border-b border-gray-600 pb-1 font-semibold">

            <div class="flex-1">
                Particulars
            </div>

            <div class="w-40 text-right">
                Amount
            </div>

        </div>

        
        <?php $__currentLoopData = $voucher; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($v->trnAccount != ($partyLedger->trnAccount ?? '')): ?>
            <?php
                $dr = (float) $v->DRAmount;
                $cr = (float) $v->CRAmount;
                //$amount = $v->DRAmount > 0 ? $v->DRAmount : $v->CRAmount;
                $amount = abs($dr) > 0 ? abs($dr) : abs($cr);
                $side = ($dr > 0) ? ' Dr' : ' Cr' ;
            ?>

            <div class="flex py-1">

                <div class="flex-1">
                    
                    <?php if($v->trnAccount != ($partyLedger->trnAccount ?? '')): ?>
                        <?php echo e(strtoupper($v->trnAccount)); ?>

                    <?php endif; ?>
                </div>
                <div class="w-40 text-right">
                    <?php echo e(number_format($amount,2)); ?> <?php echo e($side); ?>

                    
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    
    <div class="mt-6 border-t border-gray-600 pt-2">
        <div class="flex justify-end">
            <div class="w-40 text-right font-bold">
                <?php echo e(number_format(abs($totalDr ?: $totalCr), 2)); ?>  <?php echo e($side); ?>

                <!-- <?php echo e(abs($totalDr) . ' DR' ?: abs($totalCr) . ' Cr'); ?> -->
            </div>
        </div>
    </div>
    
    <?php if(!empty($header->Narration)): ?>
        <div class="mt-10 border-t border-gray-700 pt-2">
            <div class="text-gray-400 mb-1">
                Narration :
            </div>
            <div>
                <?php echo e($header->Narration); ?>

            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/reports/voucher_view.blade.php ENDPATH**/ ?>