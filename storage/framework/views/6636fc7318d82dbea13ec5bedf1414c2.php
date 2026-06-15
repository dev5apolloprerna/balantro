<!DOCTYPE html>
<html>
<head>

    <style>

        body{
            font-family: DejaVu Sans;
            font-size:12px;
            color:#000;
        }

        .border-bottom{
            border-bottom:1px solid #999;
        }

        .row{
            width:100%;
            clear:both;
        }

        .left{
            float:left;
        }

        .right{
            float:right;
        }

        .mt{
            margin-top:15px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th{
            border-bottom:1px solid #999;
            text-align:left;
            padding:6px;
        }

        td{
            padding:5px 6px;
        }

        .amount{
            text-align:right;
            width:150px;
        }

        .title{
            font-size:24px;
            font-weight:bold;
        }

        .total{
            border-top:1px solid #999;
            font-weight:bold;
        }

    </style>

</head>
<body>

    
    <div class="border-bottom">

        <div class="title">
            <?php echo e(strtoupper($header->vchType)); ?>

        </div>

        <div class="mt">

            <div>
                Voucher No :
                <strong><?php echo e($header->vchNo); ?></strong>
            </div>

            <div>
                Date :
                <?php echo e(date('d-M-y', strtotime($header->strVchDate))); ?>

            </div>

        </div>

    </div>

    
    <?php
        $partyLedger = $header; // $voucher->firstWhere('CRAmount', '>', 0);
    ?>

    <div class="mt border-bottom" style="padding-bottom:10px;">

        <strong>Party A/c Name :</strong>

        <?php echo e($partyLedger->trnAccount ?? ''); ?>


    </div>

    
    <div class="mt">

        <table>

            <thead>

                <tr>

                    <th>
                        Particulars
                    </th>

                    <th class="amount">
                        Amount
                    </th>

                </tr>

            </thead>

            <tbody>

                <?php $__currentLoopData = $voucher; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <?php if($v->trnAccount != ($partyLedger->trnAccount ?? '')): ?>

                        <?php

                            $dr = (float)$v->DRAmount;
                            $cr = (float)$v->CRAmount;

                            $amount =
                                abs($dr) > 0
                                ? abs($dr)
                                : abs($cr);
                            $side = ($dr > 0) ? ' Dr' : ' Cr' ;
                        ?>

                        <tr>

                            <td>
                                <?php echo e(strtoupper($v->trnAccount)); ?> 
                            </td>

                            <td class="amount">
                                <?php echo e(number_format($amount,2)); ?> <?php echo e($side); ?>

                            </td>

                        </tr>

                    <?php endif; ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <tr class="total">

                    <td></td>

                    <td class="amount">

                        <?php echo e(number_format(abs($totalDr ?: $totalCr),2)); ?> <?php echo e($side); ?>


                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</body>
</html><?php /**PATH D:\xampp\htdocs\balantro\resources\views\reports\pdf\voucher_pdf.blade.php ENDPATH**/ ?>