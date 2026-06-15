<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Profit & Loss Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
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

        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #f8f9fa;
            padding: 8px;
            font-weight: bold;
            border-bottom: 1px solid #dee2e6;
            font-size: 13px;
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
            font-family: DejaVu Sans Mono, monospace;
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

        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
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

        .stock-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .stock-item {
            text-align: center;
            flex: 1;
        }

        .stock-value {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        .stock-label {
            font-size: 11px;
            color: #7f8c8d;
        }

        .income-section,
        .expenses-section,
        .gross-section,
        .indirect-income-section,
        .indirect-expenses-section,
        .net-section {
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            overflow: hidden;
        }

        .section-header {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            font-weight: bold;
            font-size: 13px;
        }

        .section-content {
            padding: 15px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .total-row-pdf {
            font-weight: bold;
            background-color: #e9ecef;
            padding: 10px;
            border-top: 2px solid #dee2e6;
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

    <!-- STOCK INFORMATION -->
    {{-- <div class="stock-info">
        <div class="stock-item">
            <div class="stock-label">Opening Stock</div>
            <div class="stock-value">{{ number_format($openingStock, 2) }}</div>
        </div>
        <div class="stock-item">
            <div class="stock-label">Closing Stock</div>
            <div class="stock-value">{{ number_format($closingStock, 2) }}</div>
        </div>
        <div class="stock-item">
            <div class="stock-label">Cost of Goods Sold</div>
            <div class="stock-value">{{ number_format($cogs, 2) }}</div>
        </div>
    </div> --}}

    <!-- INCOME SECTION -->
    <div class="income-section">
        <div class="section-header">INCOME</div>
        <div class="section-content">
            <div class="item-row">
                <span>Sales Accounts</span>
                <span class="amount">{{ number_format($salesAccounts, 2) }}</span>
            </div>
            <div class="item-row">
                <span>Direct Incomes</span>
                <span class="amount">{{ number_format($directIncomes, 2) }}</span>
            </div>
            <div class="item-row">
                <span>Closing Stock</span>
                <span class="amount">{{ number_format($closingStock, 2) }}</span>
            </div>
            <div class="total-row-pdf">
                <span>Total</span>
                <span class="amount">{{ number_format($totalIncome, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- EXPENSES SECTION -->
    <div class="expenses-section">
        <div class="section-header">EXPENSES</div>
        <div class="section-content">
            <div class="item-row">
                <span>Opening Stock</span>
                <span class="amount">{{ number_format($openingStock, 2) }}</span>
            </div>
            <div class="item-row">
                <span>Purchase Accounts</span>
                <span class="amount">{{ number_format($purchaseAccounts, 2) }}</span>
            </div>
            <div class="item-row">
                <span>Direct Expenses</span>
                <span class="amount">{{ number_format($directExpenses, 2) }}</span>
            </div>
            <div class="total-row-pdf">
                <span>Total</span>
                <span class="amount">{{ number_format($totalExpenses, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- GROSS PROFIT/LOSS SECTION -->
    <div class="gross-section">
        <div class="section-header">GROSS PROFIT/LOSS</div>
        <div class="section-content">
            <div class="item-row">
                <span>Gross Profit/Loss</span>
                <span class="amount {{ $grossIsProfit ? 'profit' : 'loss' }}">
                    {{ $grossIsProfit ? '+' : '-' }}{{ number_format($grossAbs, 2) }}
                </span>
            </div>
        </div>
    </div>

    <!-- INDIRECT INCOME SECTION -->
    <div class="indirect-income-section">
        <div class="section-header">INDIRECT INCOME</div>
        <div class="section-content">
            @if (!empty($iInc))
                @foreach ($iInc as $row)
                    <div class="item-row">
                        <span>{{ $row['strGroupName'] ?? '—' }}</span>
                        <span class="amount">{{ number_format((float) $row['decMainAmount'], 2) }}</span>
                    </div>
                @endforeach
            @else
                <div class="item-row">
                    <span>No Indirect Income</span>
                    <span class="amount">0.00</span>
                </div>
            @endif
            <div class="total-row-pdf">
                <span>Total Indirect Income</span>
                <span class="amount">{{ number_format($indirectIncome, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- INDIRECT EXPENSES SECTION -->
    <div class="indirect-expenses-section">
        <div class="section-header">INDIRECT EXPENSES</div>
        <div class="section-content">
            @if (!empty($iExp))
                @foreach ($iExp as $row)
                    <div class="item-row">
                        <span>{{ $row['strGroupName'] ?? '—' }}</span>
                        <span class="amount">{{ number_format(abs((float) $row['decMainAmount']), 2) }}</span>
                    </div>
                @endforeach
            @else
                <div class="item-row">
                    <span>No Indirect Expenses</span>
                    <span class="amount">0.00</span>
                </div>
            @endif
            <div class="total-row-pdf">
                <span>Total Indirect Expenses</span>
                <span class="amount">{{ number_format($indirectExpenses, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- NET PROFIT/LOSS SECTION -->
    <div class="net-section">
        <div class="section-header">NET PROFIT/LOSS</div>
        <div class="section-content">
            <div class="item-row">
                <span>Net Profit</span>
                <span class="amount {{ $netIsProfit ? 'profit' : 'loss' }}">
                    {{ $netIsProfit ? '+' : '-' }}{{ number_format($netAbs, 2) }}
                </span>
            </div>
        </div>
    </div>

    <!-- SUMMARY -->
    <div class="summary">
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
    </div>

    <div style="margin-top: 30px; text-align: center; color: #7f8c8d; font-size: 10px;">
        Generated on: {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}
    </div>
</body>

</html>
