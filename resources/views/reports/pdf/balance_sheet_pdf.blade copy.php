<!DOCTYPE html>
<html>

<head>
    <title>Balantro - Balance Sheet</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #2c3e50;
        }

        .header .period {
            font-size: 14px;
            margin: 5px 0;
            color: #7f8c8d;
        }

        .summary-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .summary-table td {
            padding: 8px 4px;
            border-bottom: 1px solid #ddd;
        }

        .summary-value {
            text-align: right;
            font-weight: bold;
            font-size: 14px;
        }

        .section-header {
            background-color: #4F81BD;
            color: white;
            font-weight: bold;
            padding: 8px;
            margin-top: 15px;
            border-radius: 4px;
        }

        .subsection-header {
            background-color: #e9ecef;
            font-weight: bold;
            padding: 6px;
            margin-top: 10px;
        }

        .account-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .account-table td {
            padding: 4px 8px;
            border-bottom: 1px solid #eee;
        }

        .account-name {
            padding-left: 20px !important;
        }

        .amount {
            text-align: right;
            font-family: monospace;
        }

        .subtotal {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .grand-total {
            font-weight: bold;
            background-color: #e9ecef;
            border-top: 2px solid #333;
            font-size: 14px;
        }

        .difference-note {
            margin-top: 20px;
            padding: 10px;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Balantro - Balance Sheet</h1>
        <div class="period">Period: {{ date('d-m-Y', strtotime($from)) }} to {{ date('d-m-Y', strtotime($to)) }}</div>
    </div>

    @php
        $assets = [];
        $liabilities = [];
        $equity = [];
        $rows = $data['rows'] ?? [];

        // Categorize rows
        foreach ($rows as $row) {
            $group = $row->GroupName ?? ($row->strGroupName ?? '');
            $account = $row->AccountName ?? ($row->strAccountName ?? '');
            $amount = $row->Amount ?? ($row->decMainAmount ?? 0);

            if (stripos($group, 'asset') !== false) {
                $assets[$group][] = ['account' => $account, 'amount' => $amount];
            } elseif (stripos($group, 'liabil') !== false) {
                $liabilities[$group][] = ['account' => $account, 'amount' => $amount];
            } elseif (stripos($group, 'equity') !== false || stripos($group, 'capital') !== false) {
                $equity[$group][] = ['account' => $account, 'amount' => $amount];
            }
        }

        $totals = $data['totals'] ?? [];
        $totalAssets = (float) str_replace([',', 'R'], '', $totals['assets'] ?? '0');
        $totalLiabilities = (float) str_replace([',', 'R'], '', $totals['liabilities'] ?? '0');
        $totalEquity = (float) str_replace([',', 'R'], '', $totals['equity'] ?? '0');
        $difference = $totalAssets - ($totalLiabilities + $totalEquity);
    @endphp

    <!-- Summary Section -->
    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td width="30%"><strong>Total Assets</strong></td>
                <td class="summary-value">R{{ number_format($totalAssets, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Liabilities</strong></td>
                <td class="summary-value">R{{ number_format($totalLiabilities, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Equity</strong></td>
                <td class="summary-value">R{{ number_format($totalEquity, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="border-top: 2px solid #333; padding-top: 10px;">
                    <strong>Balance Status</strong>
                </td>
            </tr>
            <tr>
                <td><strong>Difference:</strong></td>
                <td class="summary-value">R{{ number_format($difference, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Assets Section -->
    <div class="section-header">Assets (Dr)</div>

    @php $assetsTotal = 0; @endphp
    @foreach ($assets as $groupName => $accounts)
        <div class="subsection-header">{{ $groupName }}</div>
        <table class="account-table">
            @php $groupTotal = 0; @endphp
            @foreach ($accounts as $account)
                <tr>
                    <td class="account-name">{{ $account['account'] }}</td>
                    <td class="amount">{{ number_format($account['amount'], 2) }}</td>
                </tr>
                @php
                    $groupTotal += $account['amount'];
                    $assetsTotal += $account['amount'];
                @endphp
            @endforeach
            <tr class="subtotal">
                <td>Subtotal</td>
                <td class="amount">{{ number_format($groupTotal, 2) }}</td>
            </tr>
        </table>
    @endforeach

    <table class="account-table">
        <tr class="grand-total">
            <td>Total Assets (Dr)</td>
            <td class="amount">R{{ number_format($assetsTotal, 2) }}</td>
        </tr>
    </table>

    <!-- Liabilities & Equity Section -->
    <div class="section-header" style="margin-top: 30px;">Liabilities & Equity (Cr)</div>

    @php
        $liabilitiesTotal = 0;
        $equityTotal = 0;
    @endphp

    <!-- Liabilities -->
    @foreach ($liabilities as $groupName => $accounts)
        <div class="subsection-header">{{ $groupName }}</div>
        <table class="account-table">
            @php $groupTotal = 0; @endphp
            @foreach ($accounts as $account)
                <tr>
                    <td class="account-name">{{ $account['account'] }}</td>
                    <td class="amount">{{ number_format($account['amount'], 2) }}</td>
                </tr>
                @php
                    $groupTotal += $account['amount'];
                    $liabilitiesTotal += $account['amount'];
                @endphp
            @endforeach
            <tr class="subtotal">
                <td>Subtotal</td>
                <td class="amount">{{ number_format($groupTotal, 2) }}</td>
            </tr>
        </table>
    @endforeach

    <!-- Equity -->
    @foreach ($equity as $groupName => $accounts)
        <div class="subsection-header">{{ $groupName }}</div>
        <table class="account-table">
            @php $groupTotal = 0; @endphp
            @foreach ($accounts as $account)
                <tr>
                    <td class="account-name">{{ $account['account'] }}</td>
                    <td class="amount">{{ number_format($account['amount'], 2) }}</td>
                </tr>
                @php
                    $groupTotal += $account['amount'];
                    $equityTotal += $account['amount'];
                @endphp
            @endforeach
            <tr class="subtotal">
                <td>Subtotal</td>
                <td class="amount">{{ number_format($groupTotal, 2) }}</td>
            </tr>
        </table>
    @endforeach

    @php $totalCr = $liabilitiesTotal + $equityTotal; @endphp
    <table class="account-table">
        <tr class="grand-total">
            <td>Total Liabilities & Equity (Cr)</td>
            <td class="amount">R{{ number_format($totalCr, 2) }}</td>
        </tr>
    </table>

    <!-- Difference Note -->
    @if (abs($difference) > 0.01)
        <div class="difference-note">
            Note: Assets and Liabilities + Equity differ by R{{ number_format(abs($difference), 2) }}
        </div>
    @endif

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #6c757d;">
        Generated on: {{ date('d-m-Y H:i:s') }} | Balantro Accounting System
    </div>
</body>

</html>
