<!DOCTYPE html>
<html>

<head>
    <title>Ballantro - Ledger Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #2c3e50;
        }

        .header .period {
            font-size: 12px;
            margin: 5px 0;
            color: #7f8c8d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background-color: #4F81BD;
            color: white;
            font-weight: bold;
            padding: 8px 4px;
            text-align: left;
            border: 1px solid #ddd;
        }

        td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            font-size: 9px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .group-total {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .grand-total {
            background-color: #e6e6e6;
            font-weight: bold;
        }

        .summary-section {
            margin-top: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
        }

        .group-header{
            background:#1f2937;
            color:#ffffff;
            font-weight:bold;
            font-size:12px;
        }

        .group-total{
            background:#f3f4f6;
            font-weight:bold;
        }

        .grand-total{
            background:#d1d5db;
            font-weight:bold;
        }
    </style>
</head>

<body>
    <!-- <div class="header">
        <h1>Ballantro - Ledger Report</h1>
		<div class="period"><strong> Name : <?php echo e($partyName ?? '-'); ?> </strong></div>
        <div class="period">Period: <?php echo e($from); ?> to <?php echo e($to); ?></div>
    </div> -->
    <div class="header">
        <div style="font-size:20px;font-weight:bold;color:#000;">
            <?php echo e(strtoupper($partyName ?? 'COMPANY NAME')); ?>

        </div>
        <?php if(!empty($companyAddress)): ?>
            <div class="period" style="color:#000;font-size:11px;">
                <?php echo nl2br(e($companyAddress)); ?>

            </div>
        <?php endif; ?>
        <?php if(!empty($companyEmail)): ?>
            <div class="period" style="color:#000;font-size:11px;">
                E-Mail : <?php echo e($companyEmail); ?>

            </div>
        <?php endif; ?>
        <div style="font-size:18px;font-weight:bold;margin-top:10px;">
            Ledger Report
        </div>
        <div class="period" style="color:#000;">
            <?php echo e(date('d-M-y', strtotime($from))); ?>

            to
            <?php echo e(date('d-M-y', strtotime($to))); ?>

        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="25%">Ledger</th>
                <th width="20%">Parent</th>
                <!-- <th width="10%" class="text-center">Side</th> -->
                <th width="15%" class="text-right">Opening</th>
                <th width="15%" class="text-right">DR</th>
                <th width="15%" class="text-right">CR</th>
                <th width="15%" class="text-right">Closing</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $grandDr = 0;
                $grandCr = 0;
                $grandOpening = 0;
                $grandClosing = 0;
                $rows = $data['rows'] ?? [];

                // Use the same helper functions as in blade file
                $toFloat = function ($v) {
                    if ($v === null || $v === '') {
                        return 0.0;
                    }
                    return (float) str_replace(',', '', (string) $v);
                };

                $inr = function ($num) {
                    $num = (float) $num;
                    $sign = $num < 0 ? '-' : '';
                    $n = abs($num);
                    $str = sprintf('%.2f', $n);
                    [$int, $dec] = explode('.', $str);
                    if (strlen($int) > 3) {
                        $last3 = substr($int, -3);
                        $rest = substr($int, 0, -3);
                        $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
                        $int = $rest . ',' . $last3;
                    }
                    return $sign . $int . '.' . $dec;
                };

                // Group by parent
                //$byParent = collect($rows)->groupBy('strParents');
                $filteredRows = collect($rows)->filter(function ($r) use ($toFloat) {
                    $op = $toFloat($r->decOpBl ?? 0);
                    $dr = $toFloat($r->decDr ?? 0);
                    $cr = $toFloat($r->decCr ?? 0);
                    $cl = $toFloat($r->decClBl ?? 0);

                    return !($op == 0 && $dr == 0 && $cr == 0 && $cl == 0);
                });

                $byParent = $filteredRows->groupBy('strParents');
                /* $byParent = $filteredRows
                    ->sortBy('strParents')
                    ->groupBy('strParents'); */
            ?>

            <?php $__currentLoopData = $byParent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent => $ledgers): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $groupDr = 0;
                    $groupCr = 0;
                    $groupOpening = 0;
                    $groupClosing = 0;
                ?>
                <tr class="group-header">
                    <td colspan="6" style="padding:10px;">
                        <?php echo e(strtoupper($parent ?: 'UNGROUPED')); ?>

                    </td>
                </tr>
                <?php $__currentLoopData = $ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $opening = $toFloat($ledger->decOpBl ?? 0);
                        $closing = $toFloat($ledger->decClBl ?? 0);
                        $drAmount = $toFloat($ledger->decDr ?? 0);
                        $crAmount = $toFloat($ledger->decCr ?? 0);

                        // Determine side using the same logic as blade file
                        $side = 'Dr';
                        if ($drAmount > $crAmount) {
                            $side = 'Dr';
                        } elseif ($crAmount > $drAmount) {
                            $side = 'Cr';
                        } else {
                            $side = $closing < 0 ? 'Cr' : 'Dr';
                        }

                        // $groupDr += $drAmount;
                        // $groupCr += $crAmount;
                        $groupDr += abs($drAmount);
                        $groupCr += abs($crAmount);

                        $groupOpening += $opening;
                        $groupClosing += $closing;

                        // $grandDr += $drAmount;
                        // $grandCr += $crAmount;
                        $grandDr += abs($drAmount);
                        $grandCr += abs($crAmount);

                        $grandOpening += $opening;
                        $grandClosing += $closing;
                    ?>
                    <tr>
                        <td><?php echo e($ledger->strCustomerName ?? 'Ledger'); ?></td>
                        <td><?php echo e($parent ?: '-'); ?></td>
                        <!-- <td class="text-center"><?php echo e($side); ?></td> -->
                        <td class="text-right">
                            <?php
                                $opSide = $opening <= 0 ? 'Dr' : 'Cr';
                            ?>
                            <?php if(abs($opening) > 0): ?>
                                <?php echo e($inr(abs($opening))); ?> <?php echo e($opening < 0 ? 'Dr' : 'Cr'); ?>

                            <?php else: ?>
                                0.00
                            <?php endif; ?>
                        </td>
                        <td class="text-right"><?php echo e($inr($drAmount)); ?></td>
                        <td class="text-right"><?php echo e($inr($crAmount)); ?></td>
                        <td class="text-right">
                            <?php
                                $side = $closing <= 0 ? 'Dr' : 'Cr';
                            ?>
                            <?php if(abs($closing) > 0): ?>
                                <?php echo e($inr(abs($closing))); ?> <?php echo e($closing < 0 ? 'Dr' : 'Cr'); ?>

                            <?php else: ?>
                                0.00
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Group Total Row -->
                <tr class="group-total">
                    <td colspan="2"><strong>Total</strong></td>
                    <!-- <td class="text-right">-</td> -->
                     <td class="text-right">
                        <?php if(abs($groupOpening) > 0): ?>
                            <?php echo e($inr(abs($groupOpening))); ?>

                            <?php echo e($groupOpening < 0 ? 'Dr' : 'Cr'); ?>

                        <?php else: ?>
                            0.00
                        <?php endif; ?>
                    </td>
                    <td class="text-right"><?php echo e($inr($groupDr)); ?></td>
                    <td class="text-right"><?php echo e($inr($groupCr)); ?></td>
                    <td class="text-right">
                        <?php if(abs($groupClosing) > 0): ?>
                            <?php echo e($inr(abs($groupClosing))); ?>

                            <?php echo e($groupClosing < 0 ? 'Dr' : 'Cr'); ?>

                        <?php else: ?>
                            0.00
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <!-- Grand Total Row -->
            <tr class="grand-total">
                <td colspan="2">Total Ledgers: <?php echo e($filteredRows->count()); ?></td>
                <td class="text-right">-</td>
                <td class="text-right"><?php echo e($inr($grandDr)); ?></td>
                <td class="text-right"><?php echo e($inr($grandCr)); ?></td>
                <td class="text-right">
                    <?php if(abs($grandClosing) > 0): ?>
                        <?php echo e($inr(abs($grandClosing))); ?>

                        <?php echo e($grandClosing < 0 ? 'Dr' : 'Cr'); ?>

                    <?php else: ?>
                        0.00
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- <div class="summary-section">
        <table>
            <tr>
                <td width="30%"><strong>Total Debit:</strong></td>
                <td><?php echo e($inr($grandDr)); ?></td>
            </tr>
            <tr>
                <td><strong>Total Credit:</strong></td>
                <td><?php echo e($inr($grandCr)); ?></td>
            </tr>
            <tr>
                <td><strong>Net Balance:</strong></td>
                <td><?php echo e($inr($grandDr - $grandCr)); ?></td>
            </tr>
        </table>
    </div> -->
    <div class="summary-section">
        <table width="100%">
            <tr>
                <td width="50%"><strong>Opening Balance:</strong></td>
                <td width="50%">
                    <?php if(abs($grandOpening) > 0): ?>
                        <?php echo e($inr(abs($grandOpening))); ?>

                        <?php echo e($grandOpening < 0 ? 'Dr' : 'Cr'); ?>

                    <?php else: ?>
                        0.00
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td width="50%"><strong>Total Debit:</strong></td>
                <td width="50%"><?php echo e($inr($grandDr)); ?></td>
            </tr>

            <tr>
                <td><strong>Total Credit:</strong></td>
                <td><?php echo e($inr($grandCr)); ?></td>
            </tr>
            <tr>
                <td><strong>Closing Balance:</strong></td>
                <td>
                    <?php if(abs($grandClosing) > 0): ?>
                        <?php echo e($inr(abs($grandClosing))); ?>

                        <?php echo e($grandClosing < 0 ? 'Dr' : 'Cr'); ?>

                    <?php else: ?>
                        0.00
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generated on: <?php echo e(date('d-m-Y H:i:s')); ?> | Ballantro Accounting System
    </div>
</body>

</html>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views/reports/pdf/ledger_pdf.blade.php ENDPATH**/ ?>