<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Profit & Loss Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            padding: 15px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 18px;
        }

        .period {
            color: #7f8c8d;
            margin-bottom: 10px;
        }


        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .table th,
        .table td {
            padding: 6px 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .amount {
            text-align: right;
            font-family: monospace;
        }

        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .account-section {
            margin-bottom: 25px;
        }

        .account-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }

        .net-profit {
            font-size: 14px;
            font-weight: bold;
            color: #28a745;
            border-top: 2px solid #dee2e6;
            padding-top: 10px;
        }

        .net-loss {
            color: #dc3545;
        }

        .page-break {
            page-break-before: always;
        }

        .stock-label {
            font-size: 11px;
            color: #7f8c8d;
        }

        .section-header {
            background: #4F81BD;
            color: #fff;
            padding: 6px;
            font-weight: bold;
            margin-top: 15px;
        }



        .item-row:last-child {
            border-bottom: none;
        }


        .formula {
            font-style: italic;
            color: #7f8c8d;
            font-size: 11px;
        }

        .profit {
            color: #28a745;
            font-weight: bold;
        }

        .loss {
            color: #dc3545;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #4F81BD;
            color: white;
            font-weight: bold;
            padding: 8px 4px;
            text-align: left;
            border: 1px solid #ddd;
        }


    .text-right {
        text-align: right;
    }

    .group-total {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .grand-total {
        font-weight: bold;
        background: #e9ecef;
        border-top: 2px solid #333;
    }
    </style>
</head>

<body>
    @php
        // Replicate the same calculation logic from blade view
        $cr = $pl['cr'] ?? [];
        $dr = $pl['dr'] ?? [];
        $iInc = $pl['IndirectIncomes'] ?? [];
        $iExp = $pl['IndirectExpenses'] ?? [];

        // Get stock values from the data
        $openingStock = (float) ($pl['OpeningStock'] ?? 0);
        $closingStock = (float) ($pl['ClosingStock'] ?? 0);

        $fmt = function (float $n): string {
            return '₹ ' . number_format($n, 2);
        };

        $sum = function (array $rows, bool $absNeg = false): float {
            $t = 0.0;
            foreach ($rows as $r) {
                $v = (float) ($r['decMainAmount'] ?? 0);
                $t += $absNeg ? abs($v) : $v;
            }
            return $t;
        };

        // Calculate values according to Excel format
        $salesAccounts = $sum(
            array_filter($cr, function ($item) {
                return ($item['strGroupName'] ?? '') === 'Sales Accounts';
            }),
            false,
        );

        $directIncomes = $sum(
            array_filter($cr, function ($item) {
                return ($item['strGroupName'] ?? '') === 'Direct Incomes';
            }),
            false,
        );

        $purchaseAccounts = $sum(
            array_filter($dr, function ($item) {
                return ($item['strGroupName'] ?? '') === 'Purchase Accounts';
            })
        );

        $directExpenses = $sum(
            array_filter($dr, function ($item) {
                return ($item['strGroupName'] ?? '') === 'Direct Expenses';
            })
        );

        $indirectIncome = $sum($iInc, false);
        $indirectExpenses = $sum($iExp);

        // Excel format calculations
        // $totalIncome = $salesAccounts + $directIncomes + $closingStock; // A + B + C = D 
        // $totalExpenses = $openingStock + $purchaseAccounts + $directExpenses; // E + F + G = H 

        // ---------------- EXCEL FORMAT CALCULATIONS ----------------

        $totalIncome =
            $salesAccounts
            + $directIncomes
            + max($closingStock, 0)
            + max(-$openingStock, 0);

        $totalExpenses =
            max($openingStock, 0)
            + $purchaseAccounts
            + $directExpenses
            + abs(min($closingStock, 0));

        // Gross Profit/Loss
        $grossProfitLoss = $totalIncome - $totalExpenses;
        $grossIsProfit = $grossProfitLoss >= 0;
        $grossAbs = abs($grossProfitLoss);

        // Net Profit
        $netProfit =
            $grossProfitLoss
            + $indirectIncome
            - $indirectExpenses;

        $netIsProfit = $netProfit >= 0;
        $netAbs = abs($netProfit);

        // Charts
        $directCr = abs($sum($cr, false));
        $directDr = abs($sum($dr, true));
        $indirectCr = abs($sum($iInc, false));
        $indirectDr = abs($sum($iExp, true));

        $totalIncomeForCharts = $directCr + $indirectCr;
        $totalExpensesForCharts = $directDr + $indirectDr;

        // COGS
        $cogs =
            $openingStock
            + $purchaseAccounts
            + $directExpenses
            - $closingStock;

        // Gross Profit/Loss calculation (Excel format)
        $grossProfitLoss = $totalIncome - $totalExpenses; // D - H = I
        $grossIsProfit = $grossProfitLoss >= 0;
        $grossAbs = abs($grossProfitLoss);

        // Net Profit calculation (Excel format)
        $netProfit = $grossProfitLoss + $indirectIncome - $indirectExpenses; // I + J - K = L
        $netIsProfit = $netProfit >= 0;
        $netAbs = abs($netProfit);

        // Calculate COGS
        $cogs = $openingStock + $purchaseAccounts + $directExpenses - $closingStock;
    @endphp

    <!-- <div class="header">
        <h1>Profit & Loss Statement</h1>
        <div class="period"><strong>Name: {{ $partyName ?? '-' }}</strong></div>
        <div class="period">
            Period: {{ $from ? \Carbon\Carbon::parse($from)->format('d M Y') : 'Start' }}
            to {{ $to ? \Carbon\Carbon::parse($to)->format('d M Y') : 'End' }}
        </div>
    </div> -->
    <div class="header">

        <div style="font-size:22px;font-weight:bold;color:#000;">
            {{ strtoupper($partyName ?? '-') }}
        </div>

        <div style="font-size:12px;color:#000;margin-top:4px;">
            {{ $companyAddress ?? '' }}
        </div>

        <div style="font-size:12px;color:#000;">
            E-Mail : {{ $companyEmail ?? '' }}
        </div>

        <div style="margin-top:12px;font-size:20px;font-weight:bold;color:#000;">
            Profit & Loss A/c
        </div>

        <div style="margin-top:5px;font-size:12px;color:#000;">
            {{ $from ? \Carbon\Carbon::parse($from)->format('d-M-y') : 'Start' }}
            to
            {{ $to ? \Carbon\Carbon::parse($to)->format('d-M-y') : 'End' }}
        </div>

    </div>

    {{-- ================= INCOME ================= --}}
    <div class="section-header">Income</div>

    <table>

        <tr>
            <td>Sales Accounts</td>
            <td class="amount">{{ number_format($salesAccounts,2) }}</td>
        </tr>

        <tr>
            <td>Direct Incomes</td>
            <td class="amount">{{ number_format($directIncomes,2) }}</td>
        </tr>

        <tr>
            <td>Closing Stock</td>
            <td class="amount">{{ number_format($closingStock,2) }}</td>
        </tr>

        <tr class="grand-total">
            <td>Total Income</td>
            <td class="amount">{{ number_format($totalIncome,2) }}</td>
        </tr>

    </table>

    {{-- ================= EXPENSES ================= --}}
    <div class="section-header">Expenses</div>

    <table>

        <tr>
            <td>Opening Stock</td>
            <td class="amount">{{ number_format($openingStock,2) }}</td>
        </tr>

        <tr>
            <td>Purchase Accounts</td>
            <td class="amount">{{ number_format($purchaseAccounts,2) }}</td>
        </tr>

        <tr>
            <td>Direct Expenses</td>
            <td class="amount">{{ number_format($directExpenses,2) }}</td>
        </tr>

        <tr class="grand-total">
            <td>Total Expenses</td>
            <td class="amount">{{ number_format($totalExpenses,2) }}</td>
        </tr>

    </table>

    {{-- ================= GROSS ================= --}}
    <div class="section-header">
        Gross Profit / Loss
    </div>

    <table>
        <tr class="grand-total">
            <td>
                {{ $grossIsProfit ? 'Gross Profit' : 'Gross Loss' }}
            </td>

            <td class="amount">
                {{ $grossIsProfit ? '+' : '-' }}
                {{ number_format($grossAbs,2) }}
            </td>
        </tr>
    </table>

    {{-- ================= INDIRECT INCOME ================= --}}
    <div class="section-header">Indirect Income</div>

    <table>

        @foreach($iInc as $row)
            <tr>
                <td>{{ $row['strGroupName'] ?? '-' }}</td>

                <td class="amount">
                    {{ number_format((float)$row['decMainAmount'],2) }}
                </td>
            </tr>
        @endforeach

        <tr class="grand-total">
            <td>Total Indirect Income</td>

            <td class="amount">
                {{ number_format($indirectIncome,2) }}
            </td>
        </tr>

    </table>

    {{-- ================= INDIRECT EXPENSE ================= --}}
    <div class="section-header">Indirect Expenses</div>

    <table>

        @foreach($iExp as $row)
            <tr>
                <td>{{ $row['strGroupName'] ?? '-' }}</td>

                <td class="amount">
                    {{ number_format((float)$row['decMainAmount'],2) }}
                </td>
            </tr>
        @endforeach

        <tr class="grand-total">
            <td>Total Indirect Expenses</td>

            <td class="amount">
                {{ number_format($indirectExpenses,2) }}
            </td>
        </tr>

    </table>

    {{-- ================= NET PROFIT ================= --}}
    <div class="section-header">
        Net Profit / Loss
    </div>

    <table>

        <tr class="grand-total">

            <td>
                {{ $netIsProfit ? 'Net Profit' : 'Net Loss' }}
            </td>

            <td class="amount">
                {{ $netIsProfit ? '+' : '-' }}
                {{ number_format($netAbs,2) }}
            </td>

        </tr>

    </table>

    <!-- SUMMARY -->
    <!-- <div class="summary">
        <div class="summary-item">
            <span><strong>Total Income:</strong></span>
            <span><strong>{{ number_format($totalIncome, 2) }}</strong></span>
        </div>
        <div class="summary-item">
            <span><strong>Total Expenses:</strong></span>
            <span><strong>{{ number_format($totalExpenses, 2) }}</strong></span>
        </div>
        <div class="summary-item">
            <span><strong>Gross {{ $grossIsProfit ? 'Profit' : 'Loss' }}:</strong></span>
            <span><strong
                    class="{{ $grossIsProfit ? 'profit' : 'loss' }}">{{ number_format($grossAbs, 2) }}</strong></span>
        </div>
        <div class="summary-item {{ $netIsProfit ? 'net-profit' : 'net-loss' }}">
            <span><strong>Net {{ $netIsProfit ? 'Profit' : 'Loss' }}:</strong></span>
            <span><strong>{{ number_format($netAbs, 2) }}</strong></span>
        </div>
    </div> -->
    {{-- ================= SUMMARY ================= --}}
<div class="section-header">
    Summary
</div>

@php

    $difference = $totalIncome - $totalExpenses;

@endphp

<table>

    <tr>
        <td><strong>Total Income</strong></td>

        <td class="amount">
            {{ number_format($totalIncome, 2) }}
        </td>
    </tr>

    <tr>
        <td><strong>Total Expenses</strong></td>

        <td class="amount">
            {{ number_format($totalExpenses, 2) }}
        </td>
    </tr>

    <tr>
        <td>
            <strong>
                Gross {{ $grossIsProfit ? 'Profit' : 'Loss' }}
            </strong>
        </td>

        <td class="amount">
            {{ number_format($grossAbs, 2) }}
        </td>
    </tr>

    <tr>
        <td><strong>Total Indirect Income</strong></td>

        <td class="amount">
            {{ number_format($indirectIncome, 2) }}
        </td>
    </tr>

    <tr>
        <td><strong>Total Indirect Expenses</strong></td>

        <td class="amount">
            {{ number_format($indirectExpenses, 2) }}
        </td>
    </tr>

    <tr class="grand-total">

        <td>
            <strong>
                Net {{ $netIsProfit ? 'Profit' : 'Loss' }}
            </strong>
        </td>

        <td class="amount">
            <strong>
                {{ number_format($netAbs, 2) }}
            </strong>
        </td>

    </tr>

</table>

    <div style="margin-top:25px;text-align:center;font-size:10px;color:#777">
        Generated on {{ date('d-m-Y H:i:s') }} | Balantro Accounting System
    </div>
</body>

</html>
