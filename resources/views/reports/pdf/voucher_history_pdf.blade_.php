<!DOCTYPE html>
<html>

<head>
    <title>Balantro - Voucher History</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
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
            font-size: 16px;
            margin: 0;
            color: #2c3e50;
        }

        .header .subtitle {
            font-size: 12px;
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
            padding: 4px 8px;
            border-bottom: 1px solid #ddd;
        }

        .summary-value {
            text-align: right;
            font-weight: bold;
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
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .special-row {
            background-color: #e3f2fd;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }
        .header{
            text-align:center;
            margin-bottom:20px;
            border-bottom:1px solid #999;
            padding-bottom:10px;
        }
    </style>
</head>

<body>
    
    <!-- <div class="header">
        <h1>Balantro - Voucher History Report</h1>
		<div class="period"><strong> Name : {{ $partyName ?? '-' }} </strong></div>
		
        <div class="subtitle">Ledger #{{ $ledgerName ?? '' }} • Period: {{ $from ? date('d-m-Y', strtotime($from)) : '' }} to
            {{ $to ? date('d-m-Y', strtotime($to)) : '' }}</div>
    </div> -->
    <div class="header">
    
        <div style="font-size:20px;font-weight:bold;color:#000;">
            {{ strtoupper($partyName ?? '-') }}
        </div>

        @if(!empty($companyAddress))
            <div style="font-size:11px;margin-top:4px;line-height:16px;">
                {!! nl2br(e($companyAddress)) !!}
            </div>
        @endif

        @if(!empty($companyEmail))
            <div style="font-size:11px;margin-top:2px;">
                E-Mail : {{ $companyEmail }}
            </div>
        @endif

        <div style="font-size:18px;font-weight:bold;margin-top:10px;">
            Ledger History
        </div>

        <div class="subtitle" style="margin-top:4px;">
            {{ $ledgerName ?? '' }}
        </div>

        <div class="subtitle">
            {{ $from ? date('d-M-y', strtotime($from)) : '' }}
            to
            {{ $to ? date('d-M-y', strtotime($to)) : '' }}
        </div>

    </div>

    <table>
        <thead>
            <tr>
                <th width="12%">Date</th>
                <th width="15%">Voucher No</th>
                <th width="12%">Type</th>
                <th width="18%">Account</th>
                <th width="13%" class="text-right">Opening</th>
                <th width="10%" class="text-right">Dr</th>
                <th width="10%" class="text-right">Cr</th>
                <th width="13%" class="text-right">Closing</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($processedRows as $row)
                @php
                
                    // Safely check for special rows
                    $isOpening = $row->is_opening ?? false;
                    $isClosing = $row->is_closing ?? false;
                    $isSpecialRow = $isOpening || $isClosing;

                    $drAmount = abs($row->DRAmount ?? 0);
                    $crAmount = abs($row->CRAmount ?? 0);

                    // $opening = $row->opening_balance ?? ($row->decRunningBalance ?? 0);
                    // $closing = $row->decRunningBalance ?? 0;
                    // $side = $row->side ?? ($closing >= 0 ? 'Dr' : 'Cr');
                    
                    //$opening = $row->opening_balance ?? 0;
                    //$closing = $row->decRunningBalance ?? 0;

                    // $side = $row->side ?? ($closing >= 0 ? 'Dr' : 'Cr');

                    /*$opening = abs($row->opening_balance ?? 0);
                    $closingRaw = $row->decRunningBalance ?? 0;
                    $closing = abs($closingRaw);
                    $side = $row->side ?? ($closingRaw >= 0 ? 'Dr' : 'Cr'); */

                    $openingRaw = (float)($row->opening_balance ?? 0);
                    $closingRaw = (float)($row->decRunningBalance ?? 0);

                    $opening = abs($openingRaw);
                    $closing = abs($closingRaw);

                    $openingSide = $openingRaw < 0 ? 'Dr' : 'Cr';
                    $closingSide = $closingRaw < 0 ? 'Dr' : 'Cr';
                @endphp
                <tr class="{{ $isSpecialRow ? 'special-row' : '' }}">
                    <td>{{ !empty($row->strVchDate) ? date('d-m-Y', strtotime($row->strVchDate)) : '' }}</td>
                    <td>{{ $row->vchNo ?? '' }}</td>
                    <td>{{ $row->vchType ?? '' }}</td>
                    <td>{{ $row->trnAccount ?? '' }}</td>
                    <td class="text-right">{{ number_format($opening, 2) }} {{ $openingSide }}</td>
                    <td class="text-right">{{ $isSpecialRow ? '0.00' : number_format($drAmount, 2) }}</td>
                    <td class="text-right">{{ $isSpecialRow ? '0.00' : number_format($crAmount, 2) }}</td>
                    <td class="text-right">{{ number_format($closing, 2) }} {{ $closingSide }}</td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>
    @php
    $firstRow = collect($processedRows)->first();

        $openingBalanceRaw = (float)($firstRow->opening_balance ?? 0);

        $openingBalance = abs($openingBalanceRaw);

        $openingSide = $openingBalanceRaw < 0 ? 'Dr' : 'Cr';

        $lastRow = collect($processedRows)->last();

        $closingBalanceRaw = (float)($lastRow->decRunningBalance ?? 0);

        $closingBalance = abs($closingBalanceRaw);

        $closingSide = $closingBalanceRaw < 0 ? 'Dr' : 'Cr';
    @endphp
	<div class="summary-section">
        <table class="summary-table">
            <tr>
                <td width="20%"><strong>Opening Balance:</strong></td>
                <td class="summary-value">{{ number_format(abs($openingBalance), 2) }} {{ $openingSide }}</td>
            </tr>
            <tr>
                <td><strong>Total Debit:</strong></td>
                <td class="summary-value">{{ number_format(abs($totalDr), 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Credit:</strong></td>
                <td class="summary-value">{{ number_format(abs($totalCr), 2) }}</td>
            </tr>
            <tr>
                <td><strong>Closing Balance:</strong></td>
                <td class="summary-value">{{ number_format(abs($closingBalance), 2) }} {{ $closingSide }}</td>
            </tr>
            @php
                //$difference = abs($totalDr) - abs($totalCr);
                $difference =
                    $openingBalanceRaw
                    - abs((float)$totalDr)
                    + abs((float)$totalCr)
                    - $closingBalanceRaw;
            @endphp

            <tr>
                <td><strong>Difference:</strong></td>
                <td class="summary-value">
                    {{ number_format(abs($difference), 2) }}
                    {{ $difference < 0 ? 'Cr' : 'Dr' }}
                </td>
            </tr>
        </table>
    </div>
    <div class="footer">
        Generated on: {{ date('d-m-Y H:i:s') }} | Balantro Accounting System
    </div>
</body>

</html>
