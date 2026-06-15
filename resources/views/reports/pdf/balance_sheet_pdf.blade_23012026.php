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
            border-collapse: collapse;
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
            border-left: 4px solid #4F81BD;
        }

        .closing-stock-header {
            background-color: #d4edda;
            font-weight: bold;
            padding: 6px;
            margin-top: 10px;
            border-left: 4px solid #28a745;
        }

        .account-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .account-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
        }

        .account-name {
            padding-left: 20px !important;
        }

        .amount {
            text-align: right;
            font-family: monospace;
            font-weight: normal;
        }

        .subtotal {
            font-weight: bold;
            background-color: #f8f9fa;
            border-top: 1px solid #ddd;
        }

        .subtotal td {
            border-top: 1px solid #ddd;
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

        .balanced-note {
            margin-top: 20px;
            padding: 10px;
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
            color: #0c5460;
        }

        .page-break {
            page-break-before: always;
        }

        .closing-stock-note {
            background-color: #d4edda;
            padding: 8px;
            margin: 10px 0;
            border-radius: 4px;
            border-left: 4px solid #28a745;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Balantro - Balance Sheet</h1>
        <div class="period"><strong> Name : {{ $partyName ?? '-' }} </strong></div>
        <div class="period">Period: {{ date('d-m-Y', strtotime($from)) }} to {{ date('d-m-Y', strtotime($to)) }}</div>
    </div>

    @php
        // Helper function to remove minus signs
        function displayAmount($amount)
        {
            return abs((float) $amount);
        }

        // Helper function for Indian number formatting without minus
        function formatINR($num)
        {
            $num = displayAmount($num);
            return number_format($num, 2);
        }

        // Define all expected groups
        $assetGroups = ['Current Assets', 'Fixed Assets', 'Investments', 'Closing Stock'];
        $liabilityGroups = ['Current Liabilities', 'Loans (Liability)'];
        $equityGroups = ['Capital Account'];

        $assets = [];
        $liabilities = [];
        $equity = [];
        $rows = $data['rows'] ?? [];

        // Get closing stock amount from totals
        $closingStockAmount = displayAmount($data['totals']['closing_stock'] ?? 0);

        // Categorize rows and remove minus signs - ensure all groups are included
        foreach ($rows as $row) {
            $group = $row->GroupName ?? ($row->strGroupName ?? '');
            $account = $row->AccountName ?? ($row->strAccountName ?? '');
            $amount = $row->Amount ?? ($row->decMainAmount ?? 0);

            // Assets
            if (
                in_array($group, $assetGroups) ||
                stripos($group, 'asset') !== false ||
                stripos($group, 'investment') !== false
            ) {
                if (!isset($assets[$group])) {
                    $assets[$group] = [];
                }
                $assets[$group][] = [
                    'account' => $account,
                    'amount' => displayAmount($amount),
                ];
            }
            // Liabilities
            elseif (in_array($group, $liabilityGroups) || stripos($group, 'liabil') !== false) {
                if (!isset($liabilities[$group])) {
                    $liabilities[$group] = [];
                }
                $liabilities[$group][] = [
                    'account' => $account,
                    'amount' => displayAmount($amount),
                ];
            }
            // Equity
            elseif (
                in_array($group, $equityGroups) ||
                stripos($group, 'equity') !== false ||
                stripos($group, 'capital') !== false
            ) {
                if (!isset($equity[$group])) {
                    $equity[$group] = [];
                }
                $equity[$group][] = [
                    'account' => $account,
                    'amount' => displayAmount($amount),
                ];
            }
        }

        // Add Closing Stock as a separate asset group if it exists
        if ($closingStockAmount > 0) {
            if (!isset($assets['Closing Stock'])) {
                $assets['Closing Stock'] = [];
            }
            $assets['Closing Stock'][] = [
                'account' => 'Closing Stock',
                'amount' => $closingStockAmount,
            ];
        }

        // Ensure all expected groups exist (even if empty)
        foreach ($assetGroups as $group) {
            if (!isset($assets[$group])) {
                $assets[$group] = [];
            }
        }
        foreach ($liabilityGroups as $group) {
            if (!isset($liabilities[$group])) {
                $liabilities[$group] = [];
            }
        }
        foreach ($equityGroups as $group) {
            if (!isset($equity[$group])) {
                $equity[$group] = [];
            }
        }

        $totals = $data['totals'] ?? [];

        // Handle both field name structures
        if (isset($totals['totalDr']) && isset($totals['totalCr'])) {
            $totalAssets = displayAmount($totals['totalDr'] ?? 0);
            // Calculate liabilities and equity from rows
            $totalLiabilities = 0;
            $totalEquity = 0;
            foreach ($rows as $row) {
                $group = $row->GroupName ?? ($row->strGroupName ?? '');
                $amount = $row->Amount ?? ($row->decMainAmount ?? 0);
                if (stripos($group, 'liabil') !== false) {
                    $totalLiabilities += displayAmount($amount);
                } elseif (stripos($group, 'equity') !== false || stripos($group, 'capital') !== false) {
                    $totalEquity += displayAmount($amount);
                }
            }
        } else {
            $totalAssets = displayAmount($totals['assets'] ?? 0);
            $totalLiabilities = displayAmount($totals['liabilities'] ?? 0);
            $totalEquity = displayAmount($totals['equity'] ?? 0);
        }

        $difference = $totalAssets - ($totalLiabilities + $totalEquity);
    @endphp

    @if ($closingStockAmount > 0)
        <div class="closing-stock-note">
            <i class="fas fa-box"></i> Closing Stock as of {{ date('d-m-Y', strtotime($to)) }}: ₹{{ formatINR($closingStockAmount) }}
        </div>
    @endif

    <!-- Assets Section -->
    <div class="section-header">Assets (Dr)</div>

    @php $assetsTotal = 0; @endphp

    <!-- Fixed Assets -->
    @if (isset($assets['Fixed Assets']) && count($assets['Fixed Assets']) > 0)
        <div class="subsection-header">Fixed Assets</div>
        <table class="account-table">
            @php $groupTotal = 0; @endphp
            @foreach ($assets['Fixed Assets'] as $account)
                <tr>
                    <td class="account-name">{{ $account['account'] ?: 'Fixed Assets' }}</td>
                    <td class="amount">{{ formatINR($account['amount']) }}</td>
                </tr>
                @php
                    $groupTotal += $account['amount'];
                    $assetsTotal += $account['amount'];
                @endphp
            @endforeach
            <tr class="subtotal">
                <td>Subtotal</td>
                <td class="amount">{{ formatINR($groupTotal) }}</td>
            </tr>
        </table>
    @endif

    <!-- Investments -->
    @if (isset($assets['Investments']) && count($assets['Investments']) > 0)
        <div class="subsection-header">Investments</div>
        <table class="account-table">
            @php $groupTotal = 0; @endphp
            @foreach ($assets['Investments'] as $account)
                <tr>
                    <td class="account-name">{{ $account['account'] ?: 'Investments' }}</td>
                    <td class="amount">{{ formatINR($account['amount']) }}</td>
                </tr>
                @php
                    $groupTotal += $account['amount'];
                    $assetsTotal += $account['amount'];
                @endphp
            @endforeach
            <tr class="subtotal">
                <td>Subtotal</td>
                <td class="amount">{{ formatINR($groupTotal) }}</td>
            </tr>
        </table>
    @endif

    <!-- Current Assets (without closing stock) -->
    @if (isset($assets['Current Assets']) && count($assets['Current Assets']) > 0)
        @php
            $currentAssetsWithoutStock = 0;
            foreach ($assets['Current Assets'] as $account) {
                $currentAssetsWithoutStock += $account['amount'];
            }
            // Subtract closing stock from current assets
            $currentAssetsWithoutStock = max(0, $currentAssetsWithoutStock - $closingStockAmount);
        @endphp
        
        @if ($currentAssetsWithoutStock > 0)
            <div class="subsection-header">Current Assets</div>
            <table class="account-table">
                <tr>
                    <td class="account-name">Current Assets (excluding stock)</td>
                    <td class="amount">{{ formatINR($currentAssetsWithoutStock) }}</td>
                </tr>
                @php $assetsTotal += $currentAssetsWithoutStock; @endphp
                <tr class="subtotal">
                    <td>Subtotal</td>
                    <td class="amount">{{ formatINR($currentAssetsWithoutStock) }}</td>
                </tr>
            </table>
        @endif
    @endif

    <!-- Closing Stock - SEPARATE LINE ITEM -->
    @if ($closingStockAmount > 0)
        <div class="closing-stock-header">
            <i class="fas fa-boxes"></i> Closing Stock
        </div>
        <table class="account-table">
            <tr>
                <td class="account-name" style="font-weight: bold;">Closing Stock</td>
                <td class="amount" style="font-weight: bold; color: #28a745;">{{ formatINR($closingStockAmount) }}</td>
            </tr>
            @php $assetsTotal += $closingStockAmount; @endphp
            <tr>
                <td colspan="2" style="text-align: right; font-size: 10px; color: #28a745; padding-top: 0;">
                    As of {{ date('d-m-Y', strtotime($to)) }}
                </td>
            </tr>
        </table>
    @endif

    <!-- Other Assets (if any) -->
    @foreach ($assets as $groupName => $accounts)
        @if (!in_array($groupName, ['Current Assets', 'Fixed Assets', 'Investments', 'Closing Stock']) && count($accounts) > 0)
            <div class="subsection-header">{{ $groupName }}</div>
            <table class="account-table">
                @php $groupTotal = 0; @endphp
                @foreach ($accounts as $account)
                    <tr>
                        <td class="account-name">{{ $account['account'] ?: $groupName }}</td>
                        <td class="amount">{{ formatINR($account['amount']) }}</td>
                    </tr>
                    @php
                        $groupTotal += $account['amount'];
                        $assetsTotal += $account['amount'];
                    @endphp
                @endforeach
                <tr class="subtotal">
                    <td>Subtotal</td>
                    <td class="amount">{{ formatINR($groupTotal) }}</td>
                </tr>
            </table>
        @endif
    @endforeach

    <table class="account-table">
        <tr class="grand-total">
            <td>Total Assets (Dr)</td>
            <td class="amount">{{ formatINR($assetsTotal) }}</td>
        </tr>
    </table>

    <!-- Liabilities & Equity Section -->
    <div class="section-header" style="margin-top: 30px;">Liabilities & Equity (Cr)</div>

    @php
        $liabilitiesTotal = 0;
        $equityTotal = 0;
    @endphp

    <!-- Current Liabilities -->
    @if (isset($liabilities['Current Liabilities']) && count($liabilities['Current Liabilities']) > 0)
        <div class="subsection-header">Current Liabilities</div>
        <table class="account-table">
            @php $groupTotal = 0; @endphp
            @foreach ($liabilities['Current Liabilities'] as $account)
                <tr>
                    <td class="account-name">{{ $account['account'] ?: 'Current Liabilities' }}</td>
                    <td class="amount">{{ formatINR($account['amount']) }}</td>
                </tr>
                @php
                    $groupTotal += $account['amount'];
                    $liabilitiesTotal += $account['amount'];
                @endphp
            @endforeach
            <tr class="subtotal">
                <td>Subtotal</td>
                <td class="amount">{{ formatINR($groupTotal) }}</td>
            </tr>
        </table>
    @endif

    <!-- Loans (Liability) -->
    @if (isset($liabilities['Loans (Liability)']) && count($liabilities['Loans (Liability)']) > 0)
        <div class="subsection-header">Loans (Liability)</div>
        <table class="account-table">
            @php $groupTotal = 0; @endphp
            @foreach ($liabilities['Loans (Liability)'] as $account)
                <tr>
                    <td class="account-name">{{ $account['account'] ?: 'Loans (Liability)' }}</td>
                    <td class="amount">{{ formatINR($account['amount']) }}</td>
                </tr>
                @php
                    $groupTotal += $account['amount'];
                    $liabilitiesTotal += $account['amount'];
                @endphp
            @endforeach
            <tr class="subtotal">
                <td>Subtotal</td>
                <td class="amount">{{ formatINR($groupTotal) }}</td>
            </tr>
        </table>
    @endif

    <!-- Other Liabilities (if any) -->
    @foreach ($liabilities as $groupName => $accounts)
        @if (!in_array($groupName, ['Current Liabilities', 'Loans (Liability)']) && count($accounts) > 0)
            <div class="subsection-header">{{ $groupName }}</div>
            <table class="account-table">
                @php $groupTotal = 0; @endphp
                @foreach ($accounts as $account)
                    <tr>
                        <td class="account-name">{{ $account['account'] ?: $groupName }}</td>
                        <td class="amount">{{ formatINR($account['amount']) }}</td>
                    </tr>
                    @php
                        $groupTotal += $account['amount'];
                        $liabilitiesTotal += $account['amount'];
                    @endphp
                @endforeach
                <tr class="subtotal">
                    <td>Subtotal</td>
                    <td class="amount">{{ formatINR($groupTotal) }}</td>
                </tr>
            </table>
        @endif
    @endforeach

    <!-- Capital Account -->
    @if (isset($equity['Capital Account']) && count($equity['Capital Account']) > 0)
        <div class="subsection-header">Capital Account</div>
        <table class="account-table">
            @php $groupTotal = 0; @endphp
            @foreach ($equity['Capital Account'] as $account)
                <tr>
                    <td class="account-name">{{ $account['account'] ?: 'Capital Account' }}</td>
                    <td class="amount">{{ formatINR($account['amount']) }}</td>
                </tr>
                @php
                    $groupTotal += $account['amount'];
                    $equityTotal += $account['amount'];
                @endphp
            @endforeach
            <tr class="subtotal">
                <td>Subtotal</td>
                <td class="amount">{{ formatINR($groupTotal) }}</td>
            </tr>
        </table>
    @endif

    <!-- Other Equity (if any) -->
    @foreach ($equity as $groupName => $accounts)
        @if ($groupName !== 'Capital Account' && count($accounts) > 0)
            <div class="subsection-header">{{ $groupName }}</div>
            <table class="account-table">
                @php $groupTotal = 0; @endphp
                @foreach ($accounts as $account)
                    <tr>
                        <td class="account-name">{{ $account['account'] ?: $groupName }}</td>
                        <td class="amount">{{ formatINR($account['amount']) }}</td>
                    </tr>
                    @php
                        $groupTotal += $account['amount'];
                        $equityTotal += $account['amount'];
                    @endphp
                @endforeach
                <tr class="subtotal">
                    <td>Subtotal</td>
                    <td class="amount">{{ formatINR($groupTotal) }}</td>
                </tr>
            </table>
        @endif
    @endforeach

    @php $totalCr = $liabilitiesTotal + $equityTotal; @endphp
    <table class="account-table">
        <tr class="grand-total">
            <td>Total Liabilities & Equity (Cr)</td>
            <td class="amount">{{ formatINR($totalCr) }}</td>
        </tr>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td width="30%"><strong>Total Assets</strong></td>
                <td class="summary-value">₹{{ formatINR($assetsTotal) }}</td>
            </tr>
            <tr>
                <td><strong>Total Liabilities</strong></td>
                <td class="summary-value">₹{{ formatINR($liabilitiesTotal) }}</td>
            </tr>
            <tr>
                <td><strong>Total Equity</strong></td>
                <td class="summary-value">₹{{ formatINR($equityTotal) }}</td>
            </tr>
            @if ($closingStockAmount > 0)
            <tr>
                <td><strong>Closing Stock</strong></td>
                <td class="summary-value" style="color: #28a745;">₹{{ formatINR($closingStockAmount) }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="2" style="border-top: 2px solid #333; padding-top: 10px;">
                    <strong>Balance Status</strong>
                </td>
            </tr>
            <tr>
                <td><strong>Difference:</strong></td>
                <td class="summary-value">₹{{ formatINR($difference) }}</td>
            </tr>
        </table>
    </div>

    <!-- Balance Note -->
    @if (abs($difference) <= 0.01)
        <div class="balanced-note">
            ✓ Balanced: Assets equal Liabilities + Equity
        </div>
    @else
        <div class="difference-note">
            Note: Assets and Liabilities + Equity differ by ₹{{ formatINR(abs($difference)) }}
        </div>
    @endif

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #6c757d;">
        Generated on: {{ date('d-m-Y H:i:s') }} | Balantro Accounting System
    </div>
</body>

</html>