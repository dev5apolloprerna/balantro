<!DOCTYPE html>
<html>
<head>
    <title>Balance Sheet</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #000;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .date-range {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 13px;
        }

        th {
            background: #f0f0f0;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        @media print {
            body {
                margin: 10px;
            }
        }
    </style>
</head>

<body>

    <h2>Balance Sheet</h2>

    <div class="date-range">
        Period: <?php echo e($from); ?> to <?php echo e($to); ?>

    </div>

    <table>
        <thead>
            <tr>
                <th>Assets (Dr)</th>
                <th class="text-right">Amount</th>
                <th>Liabilities & Equity (Cr)</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>

        <tbody>
            
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($row->asset_name ?? ''); ?></td>
                    <td class="text-right"><?php echo e(number_format($row->asset_amount ?? 0, 2)); ?></td>

                    <td><?php echo e($row->liability_name ?? ''); ?></td>
                    <td class="text-right"><?php echo e(number_format($row->liability_amount ?? 0, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>

        <tfoot>
            <tr class="bold">
                <td>Total Assets</td>
                <td class="text-right"><?php echo e(number_format($totalAssets ?? 0, 2)); ?></td>

                <td>Total Liabilities</td>
                <td class="text-right"><?php echo e(number_format($totalLiabilities ?? 0, 2)); ?></td>
            </tr>
        </tfoot>
    </table>

    
    <script>
        window.onload = function () {
            window.print();

            window.onafterprint = function () {
                window.close();
            };
        };
    </script>

</body>
</html><?php /**PATH D:\xampp\htdocs\balantro\resources\views\reports\balance_sheet_print.blade.php ENDPATH**/ ?>