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

@php
    $rows = collect($data['rows'] ?? []);
    $totals = $data['totals'] ?? [];

    $drRows = $rows->where('Side','DR');
    $crRows = $rows->where('Side','CR');

    $inr = fn($v) => number_format((float)$v, 2, '.', ',');

    $TotalDr = 0;
    $TotalCr = 0;

    $closingStock = abs((float)($totals['closing_stock'] ?? 0));
@endphp
@php
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
@endphp
<!-- <div class="header">
    <h1>Balantro – Balance Sheet</h1>
    <div class="period"><strong>{{ $partyName ?? '-' }}</strong></div>
    <div class="period">
        Period: {{ date('d-m-Y',strtotime($from)) }}
        to
        {{ date('d-m-Y',strtotime($to)) }}
    </div>
</div> -->
<div class="header" style="border-bottom:none;margin-bottom:10px;">

    {{-- Company Name --}}
    <div style="
        font-size:22px;
        font-weight:bold;
        text-align:center;
        margin-bottom:4px;
    ">
        {{ strtoupper($partyName ?? 'COMPANY NAME') }}
    </div>

    {{-- Address --}}
    <div style="
        text-align:center;
        font-size:12px;
        line-height:18px;
    ">
        {{ $companyAddress ?? 'Company Address Here' }}
    </div>

    {{-- Email --}}
    @if(!empty($companyEmail))
    <div style="
        text-align:center;
        font-size:12px;
        margin-bottom:10px;
    ">
        E-Mail : {{ $companyEmail }}
    </div>
    @endif

    {{-- Report Title --}}
    <div style="
        text-align:center;
        font-size:20px;
        font-weight:bold;
        margin-top:5px;
    ">
        Balance Sheet
    </div>

    {{-- Period --}}
    <div style="
        text-align:center;
        font-size:12px;
        margin-top:4px;
        margin-bottom:15px;
    ">
        {{ date('j-M-y', strtotime($from)) }}
        to
        {{ date('j-M-y', strtotime($to)) }}
    </div>

</div>
{{-- ================= ASSETS (DR) ================= --}}
<div class="section-header">Assets (Dr)</div>

<table>
    @php
$fixedOrder = ['Fixed Assets', 'Investments', 'Current Assets'];
$printed = [];
@endphp
@foreach ($fixedOrder as $grp)
    @foreach ($drRows->where('strGroupName', $grp) as $r)

        @php
            $amount = (float)($r->decMainAmount ?? 0);
            $amt = $amount > 0 ? -1 * $amount : abs($amount);
            $TotalDr += $amt;
            $printed[] = $r->strGroupName;
        @endphp

        <tr>
            <td>{{ $r->strGroupName }}</td>
            <td class="amount">{{ $inr($amt) }}</td>
        </tr>

    @endforeach
@endforeach
{{-- STEP 2: REMAINING --}}
@foreach ($drRows as $r)

    @if (!in_array($r->strGroupName, $printed))

        @php
            $amount = (float)($r->decMainAmount ?? 0);
            $amt = $amount > 0 ? -1 * $amount : abs($amount);
            $TotalDr += $amt;
        @endphp

        <tr>
            <td>{{ $r->strGroupName }}</td>
            <td class="amount">{{ $inr($amt) }}</td>
        </tr>

    @endif

@endforeach


{{-- Closing Stock --}}
@if ($closingStock > 0)
    <tr>
        <td><strong>Closing Stock</strong></td>
        <td class="amount"><strong>{{ $inr($closingStock) }}</strong></td>
    </tr>
    @php $TotalDr += $closingStock; @endphp
@endif
@if ($showDiffOnAssetSide)

    <tr>
        <td style="color:red;font-weight:bold;">
            Difference in Balance Sheet
        </td>

        <td class="amount" style="color:red;font-weight:bold;">
            {{ $inr($differenceAmount) }}
        </td>
    </tr>

    @php
        $TotalDr += $differenceAmount;
    @endphp

@endif
<tr class="grand-total">
    <td>Total Assets (Dr)</td>
    <td class="amount">{{ $inr($TotalDr) }}</td>
</tr>
</table>

{{-- ================= LIABILITIES & EQUITY (CR) ================= --}}
<div class="section-header">Liabilities &amp; Equity (Cr)</div>

<table>
@php
$fixedCR = ['Capital Account','Loans (Liability)','Current Liabilities','Suspense A/c','Profit & Loss A/c'];
$printedCR = [];
@endphp
{{-- FIXED --}}
@foreach ($fixedCR as $grp)
    @foreach ($crRows->where('strGroupName', $grp) as $r)

        @php
            $amt = (float)($r->decMainAmount ?? 0);
            $TotalCr += $amt;
            $printedCR[] = $r->strGroupName;
        @endphp

        <tr>
            <td>{{ $r->strGroupName }}</td>
            <td class="amount">{{ $inr($amt) }}</td>
        </tr>

    @endforeach
@endforeach

{{-- REMAINING --}}
@foreach ($crRows as $r)

    @if (!in_array($r->strGroupName, $printedCR))

        @php
            $amt = (float)($r->decMainAmount ?? 0);
            $TotalCr += $amt;
        @endphp

        <tr>
            <td>{{ $r->strGroupName }}</td>
            <td class="amount">{{ $inr($amt) }}</td>
        </tr>

    @endif

@endforeach

@if ($showDiffOnLiabilitySide)

    <tr>
        <td style="color:red;font-weight:bold;">
            Difference in Balance Sheet
        </td>

        <td class="amount" style="color:red;font-weight:bold;">
            {{ $inr($differenceAmount) }}
        </td>
    </tr>

    @php
        $TotalCr += $differenceAmount;
    @endphp

@endif

<tr class="grand-total">
    <td>Total (Cr)</td>
    <td class="amount">{{ $inr($TotalCr) }}</td>
</tr>
</table>



<div style="margin-top:25px;text-align:center;font-size:10px;color:#777">
    Generated on {{ date('d-m-Y H:i:s') }} | Balantro Accounting System
</div>

</body>
</html>
