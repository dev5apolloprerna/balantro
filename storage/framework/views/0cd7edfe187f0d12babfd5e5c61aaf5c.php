<!DOCTYPE html>
<html>
<head>
    <title>Balance Sheet</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; padding: 15px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin: 0; }
        .period { font-size: 13px; color: #555; }

        .section-header {
            background: #4F81BD; color: #fff; padding: 6px;
            font-weight: bold; margin-top: 20px;
        }

        table { width: 100%; border-collapse: collapse; }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; }
        .amount { text-align: right; font-family: monospace; }
        .grand-total { font-weight: bold; background: #e9ecef; border-top: 2px solid #333; }
        .note { margin-top: 15px; padding: 10px; font-weight: bold; text-align: center; }

        .ok { background: #d1ecf1; color: #0c5460; }
        .warn { background: #fff3cd; color: #856404; }
    </style>
</head>

<body>

<?php
    $rows = collect($data['rows'] ?? []);
    $totals = $data['totals'] ?? [];

    $drRows = $rows->where('Side','DR');
    $crRows = $rows->where('Side','CR');

    $inr = fn($v) => number_format((float)$v, 2, '.', ',');

    $TotalDr = 0;
    $TotalCr = 0;

    $closingStock = abs((float)($totals['closing_stock'] ?? 0));
?>
<?php
    foreach ($drRows as $r) {

        $amount = (float)($r->decMainAmount ?? 0);

        $TotalDr += $amount > 0
            ? -1 * $amount
            : abs($amount);
    }

    foreach ($crRows as $r) {
        $TotalCr += (float)($r->decMainAmount ?? 0);
    }

    $TotalDr += $closingStock;

    $diff = round($TotalDr - $TotalCr, 2);

    $differenceAmount = abs($diff);

    $showDiffOnAssetSide = $TotalDr < $TotalCr;
    $showDiffOnLiabilitySide = $TotalCr < $TotalDr;

    // reset for actual table rendering
    $TotalDr = 0;
    $TotalCr = 0;
?>
<!-- <div class="header">
    <h1>Balantro – Balance Sheet</h1>
    <div class="period"><strong><?php echo e($partyName ?? '-'); ?></strong></div>
    <div class="period">
        Period: <?php echo e(date('d-m-Y',strtotime($from))); ?>

        to
        <?php echo e(date('d-m-Y',strtotime($to))); ?>

    </div>
</div> -->
<div class="header" style="border-bottom:none;margin-bottom:10px;">

    
    <div style="
        font-size:22px;
        font-weight:bold;
        text-align:center;
        margin-bottom:4px;
    ">
        <?php echo e(strtoupper($partyName ?? 'COMPANY NAME')); ?>

    </div>

    
    <div style="
        text-align:center;
        font-size:12px;
        line-height:18px;
    ">
        <?php echo e($companyAddress ?? 'Company Address Here'); ?>

    </div>

    
    <?php if(!empty($companyEmail)): ?>
    <div style="
        text-align:center;
        font-size:12px;
        margin-bottom:10px;
    ">
        E-Mail : <?php echo e($companyEmail); ?>

    </div>
    <?php endif; ?>

    
    <div style="
        text-align:center;
        font-size:20px;
        font-weight:bold;
        margin-top:5px;
    ">
        Balance Sheet
    </div>

    
    <div style="
        text-align:center;
        font-size:12px;
        margin-top:4px;
        margin-bottom:15px;
    ">
        <?php echo e(date('j-M-y', strtotime($from))); ?>

        to
        <?php echo e(date('j-M-y', strtotime($to))); ?>

    </div>

</div>

<div class="section-header">Assets (Dr)</div>

<table>
    <?php
$fixedOrder = ['Fixed Assets', 'Investments', 'Current Assets'];
$printed = [];
?>
<?php $__currentLoopData = $fixedOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $drRows->where('strGroupName', $grp); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php
            $amount = (float)($r->decMainAmount ?? 0);
            $amt = $amount > 0 ? -1 * $amount : abs($amount);
            $TotalDr += $amt;
            $printed[] = $r->strGroupName;
        ?>

        <tr>
            <td><?php echo e($r->strGroupName); ?></td>
            <td class="amount"><?php echo e($inr($amt)); ?></td>
        </tr>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__currentLoopData = $drRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <?php if(!in_array($r->strGroupName, $printed)): ?>

        <?php
            $amount = (float)($r->decMainAmount ?? 0);
            $amt = $amount > 0 ? -1 * $amount : abs($amount);
            $TotalDr += $amt;
        ?>

        <tr>
            <td><?php echo e($r->strGroupName); ?></td>
            <td class="amount"><?php echo e($inr($amt)); ?></td>
        </tr>

    <?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



<?php if($closingStock > 0): ?>
    <tr>
        <td><strong>Closing Stock</strong></td>
        <td class="amount"><strong><?php echo e($inr($closingStock)); ?></strong></td>
    </tr>
    <?php $TotalDr += $closingStock; ?>
<?php endif; ?>
<?php if($showDiffOnAssetSide): ?>

    <tr>
        <td style="color:red;font-weight:bold;">
            Difference in Balance Sheet
        </td>

        <td class="amount" style="color:red;font-weight:bold;">
            <?php echo e($inr($differenceAmount)); ?>

        </td>
    </tr>

    <?php
        $TotalDr += $differenceAmount;
    ?>

<?php endif; ?>
<tr class="grand-total">
    <td>Total Assets (Dr)</td>
    <td class="amount"><?php echo e($inr($TotalDr)); ?></td>
</tr>
</table>


<div class="section-header">Liabilities &amp; Equity (Cr)</div>

<table>
<?php
$fixedCR = ['Capital Account','Loans (Liability)','Current Liabilities','Suspense A/c','Profit & Loss A/c'];
$printedCR = [];
?>

<?php $__currentLoopData = $fixedCR; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $crRows->where('strGroupName', $grp); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php
            $amt = (float)($r->decMainAmount ?? 0);
            $TotalCr += $amt;
            $printedCR[] = $r->strGroupName;
        ?>

        <tr>
            <td><?php echo e($r->strGroupName); ?></td>
            <td class="amount"><?php echo e($inr($amt)); ?></td>
        </tr>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<?php $__currentLoopData = $crRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <?php if(!in_array($r->strGroupName, $printedCR)): ?>

        <?php
            $amt = (float)($r->decMainAmount ?? 0);
            $TotalCr += $amt;
        ?>

        <tr>
            <td><?php echo e($r->strGroupName); ?></td>
            <td class="amount"><?php echo e($inr($amt)); ?></td>
        </tr>

    <?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if($showDiffOnLiabilitySide): ?>

    <tr>
        <td style="color:red;font-weight:bold;">
            Difference in Balance Sheet
        </td>

        <td class="amount" style="color:red;font-weight:bold;">
            <?php echo e($inr($differenceAmount)); ?>

        </td>
    </tr>

    <?php
        $TotalCr += $differenceAmount;
    ?>

<?php endif; ?>

<tr class="grand-total">
    <td>Total (Cr)</td>
    <td class="amount"><?php echo e($inr($TotalCr)); ?></td>
</tr>
</table>



<div style="margin-top:25px;text-align:center;font-size:10px;color:#777">
    Generated on <?php echo e(date('d-m-Y H:i:s')); ?> | Balantro Accounting System
</div>

</body>
</html>
<?php /**PATH D:\xampp\htdocs\balantro\resources\views\reports\pdf\balance_sheet_pdf.blade.php ENDPATH**/ ?>