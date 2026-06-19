@extends('layouts.super_admin')
@section('title', 'Balance Sheet')

@section('content')
@php
/* ============================================
   SIMPLE & SAFE BALANCE SHEET LOGIC
   ============================================ */

$payload = $data ?? ($resp['data'] ?? []);
$rows = collect($payload['rows'] ?? []);

$drRows = $rows->where('Side', 'DR');
$crRows = $rows->where('Side', 'CR');

/* Totals */
$totalAssets = 0;
$totalCr     = 0;
$liabs       = 0;
$equity      = 0;

/* Helpers */
$displayAmountDr = fn($v) => abs((float)$v);
$displayAmount = fn($v) => (float)$v;
//$inr = fn($v) => number_format(abs((float)$v), 2, '.', ',');
$inr = fn($v) => number_format((float)$v, 2, '.', ',');
/* Asset breakdown (design dependency) */
$currentAssetsWithoutStock = 0;
$fixedAssets               = 0;
$investments               = 0;
$otherAssets               = 0;

/* Liability breakdown (design dependency) */
$currentLiabilities  = 0;
$longTermLiabilities = 0;
$otherLiabilities    = 0;

/* ============================================
   CALCULATE ASSETS (DR)
   ============================================ */

foreach ($drRows as $r) {
    //$totalAssets += abs((float)$r->decMainAmount);
    $amount = (float) ($r->decMainAmount ?? 0);
    if ($amount > 0) {
        $totalAssets += -1 * $r->decMainAmount;
    } else {
        $totalAssets += $displayAmountDr($r->decMainAmount ?? 0);
    }
}

$fixedAssets = 0;
$investments = 0;

foreach ($drRows as $r) {
    //$amt = abs((float)$r->decMainAmount);
    if (str_contains($r->strGroupName, 'Fixed Assets')) {
        $fixedAssets +=  -1 * (float)$r->decMainAmount;
    }

    if (str_contains($r->strGroupName, 'Investment')) {
        $investments +=  -1 * (float)$r->decMainAmount;
    }
}

/* ============================================
   CALCULATE LIABILITIES & EQUITY (CR)
   ============================================ */
$currentLiabilities  = 0;
$longTermLiabilities = 0;
$otherLiabilities    = 0;
$capitalAccount = 0;
foreach ($crRows as $r) {
    $amt = (float)$r->decMainAmount;
    
    $totalCr += $amt;

    if (in_array($r->strGroupName, ['Capital Account', 'Profit & Loss A/c'])) {
        $equity += $amt;
    } else {
        $liabs += $amt;
    }

    if (str_contains($r->strGroupName, 'Current Liabilities')) {
        $currentLiabilities += $amt;
    }
    elseif (str_contains($r->strGroupName, 'Loans')) {
        $longTermLiabilities += $amt;
    } elseif (str_contains($r->strGroupName, 'Capital Account')) {
        $capitalAccount += $amt;
    }elseif (str_contains($r->strGroupName, 'Profit & Loss A/c')) {
        $capitalAccount += $amt;
    } else {
        $otherLiabilities += $amt;
    }
}
/* ============================================
   BALANCE CHECK
   ============================================ */
$balanceDiff = round($totalAssets - $totalCr, 2);

/* ============================================
   OPTIONAL (keep existing vars used in design)
   ============================================ */
$assets = $totalAssets;
@endphp
@php
/* ============================================
   DEFAULT VALUES (REQUIRED FOR EXISTING DESIGN)
   ============================================ */

/* Closing stock */
$closingStockAmount  = $payload['totals']['closing_stock'] ?? 0;
$closingStockDisplay = abs((float)$closingStockAmount);


@endphp

@php
/* ============================================
   CALCULATE CURRENT ASSETS (GROUP LEVEL)
   ============================================ */

$currentAssetsWithoutStock = 0;
$iPrimaryGroupIdcurrentAsset = 0;
foreach ($drRows as $r) {
    if (str_contains($r->strGroupName, 'Current Assets')) {
        $currentAssetsWithoutStock += abs((float)$r->decMainAmount);
        $iPrimaryGroupIdcurrentAsset = $r->iPrimaryGroupId;
    }
}
@endphp

@php

$otherAssets = max(
    0,
    $assets
    - $currentAssetsWithoutStock
    - $fixedAssets
    - $investments
    - $closingStockDisplay
);

@endphp
@php
    $differenceAmount = abs($balanceDiff);

    $showDiffOnAssetSide = $totalAssets < $totalCr;
    $showDiffOnLiabilitySide = $totalCr < $totalAssets;
@endphp
<div class="mt-1 border-b border-gray-200 dark:border-gray-700 pb-1">
    @php
        $queryParams = array_merge(request()->query(), [
            'from' => request('from', $from ?? ''),
            'to' => request('to', $to ?? ''),
            'range' => request('range', $rangeSel ?? ''),
        ]);

        $financialYearOptions = collect($financialYears ?? [])
            ->map(function ($year) {
                $label = trim((string) ($year->strYear ?? ''));
                if (! preg_match('/^(\d{4})-(\d{4})$/', $label, $matches)) {
                    return null;
                }
                return [
                    'value' => $label,
                    'label' => $label,
                    'from' => $matches[1] . '-04-01',
                    'to' => $matches[2] . '-03-31',
                ];
            })
            ->filter()
            ->values();

        $periodText = function () use ($from, $to, $rangeSel) {
            $f = date('d-m-Y', strtotime($from ?? request('from')));
            $t = date('d-m-Y', strtotime($to ?? request('to')));
            if ($f || $t) {
                return trim(($f ?: '—') . ' to ' . ($t ?: '—'));
            }
            // Add more cases as needed
            return '';
        };
    @endphp
    <div class="flex flex-wrap lg:flex-nowrap items-center justify-between gap-4">
        <!-- Left : Client Name -->
        <div class="flex items-center gap-3 shrink-0">
            <div
                class="h-10 w-10 rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 text-white flex items-center justify-center font-bold">
                {{ strtoupper(substr($user->name ?? '',0,1)) }}
            </div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                {{ strtoupper($user->name ?? '') }}
            </h1>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-2 flex-1">
            @include('admin.clients.reports.tabmanu')
        </div>
        <!-- Right : FY + Back -->
        <div class="flex items-center gap-3 shrink-0">
            <!-- <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                {{ $labelFY ?? '' }} -->
            </span>
            <a href="{{ url()->previous() }}" title="Go Back"
                class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700
                hover:border-[#f472b6] hover:shadow-[0_0_15px_#f472b6] hover:scale-105 hover:-translate-y-1">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </div>
</div>

<div class="dashboard-main-body">
    <div class="container py-3">
        <div class="flex items-center justify-between">
            <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Balance Sheet</h1>
            @php
                $asOnText = '';

                $asOnText = 'Balance Sheet as on ' . date('d-m-Y', strtotime($to));
            @endphp
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    • {{ $asOnText }}
            </p>
            </div>
            <div>
                <div class="export-buttons mb-3">
                    @php
                        $queryParams = array_merge(request()->query(), [
                            'from' => request('from', $from ?? ''),
                            'to' => request('to', $to ?? ''),
                            'range' => request('range', $rangeSel ?? ''),
                            'guid' => $guid
                        ]);
                    @endphp
                    <a href="{{ route('reports.balance-sheet.export-pdf', $queryParams) }}" title="Export into PDF"
                        class="group btn inline-block relative text-white px-4 py-2 text-sm rounded-md border border-gray-700
                                transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                    &nbsp;
                    <a href="{{ route('reports.balance-sheet.export-excel', $queryParams) }}" title="Export into Excel"
                        class="group btn inline-block relative text-white px-4 py-2 text-sm rounded-md border border-gray-700
                                transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#34d399]
                                hover:shadow-[0_0_15px_#34d399]
                                hover:scale-105
                                hover:-translate-y-1">
                        <i class="fas fa-file-excel"></i>
                    </a>

                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('clients.reports.balanceSheet',$guid) }}" id="filterForm"
            class="mt-2 rounded-lg p-2 flex flex-wrap items-end gap-3">
            @csrf
            @php
                $rangeSel = request('range', $rangeSel ?? ($financialYearOptions->first()['value'] ?? ''));
                $rangeOptions = $financialYearOptions->pluck('label', 'value')->all();
            @endphp
            <div class="relative"
                x-data="{
                    open: false,
                    selected: @js($rangeSel),
                    options: @js($rangeOptions),

                    init() {
                        this.$watch('selected', value => {
                            handleRangeChange(value);
                        });
                    }
                }">

                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Date Range</label>

                <!-- Hidden input -->
                <input type="hidden" name="range" :value="selected">

                <!-- Button -->
                <button type="button" @click="open = !open"
                    class="w-full text-left
                    bg-gradient-to-br from-white/60 to-white/30
                    dark:from-white/10 dark:to-transparent
                    backdrop-blur-xl
                    border border-gray-300/80 dark:border-cyan-400/20
                    text-gray-900 dark:text-white
                    rounded-xl px-3 py-2 pr-10 text-sm
                    focus:outline-none
                    focus:ring-2 focus:ring-[#22d3ee]
                    transition-all duration-300">

                    <span x-text="options[selected] ?? 'Select Range'"></span>
                </button>

                <!-- Arrow -->
                <div class="mt-2 pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-300">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>

                <!-- Dropdown -->
                <ul x-show="open" @click.outside="open = false"
                    x-transition
                    class="absolute z-50 mt-2 w-full rounded-xl overflow-hidden

                    bg-white/10 dark:bg-white/5
                    backdrop-blur-2xl

                    border border-white/20
                    ring-1 ring-white/10

                    shadow-[0_8px_40px_rgba(0,0,0,0.4)]"
                    style="backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);">

                    @forelse ($financialYearOptions as $financialYear)
                        <li>
                            <button type="button"
                                @click="selected = @js($financialYear['value']); open = false"
                                class="w-full text-left px-4 py-2 text-sm transition-all duration-200 text-gray-800 dark:text-white hover:bg-black/10 dark:hover:bg-white/10 hover:text-[#22d3ee]"
                                :class="selected === @js($financialYear['value']) ? 'bg-[#22d3ee]/20 text-[#22d3ee]' : ''">
                                {{ $financialYear['label'] }}
                            </button>
                        </li>
                    @empty
                        <li class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">No financial years found</li>
                    @endforelse
                    <li>
                        <button type="button"
                            @click="selected = 'custom'; open = false"
                            class="w-full text-left px-4 py-2 text-sm transition-all duration-200 text-gray-800 dark:text-white hover:bg-black/10 dark:hover:bg-white/10 hover:text-[#22d3ee]"
                            :class="selected === 'custom' ? 'bg-[#22d3ee]/20 text-[#22d3ee]' : ''">
                            Custom Date
                        </button>
                    </li>

                </ul>
            </div>
            <div id="customFromWrap" class="{{ $rangeSel === 'custom' ? '' : 'hidden' }}" style="display: none;">
                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">From Date</label>
                <input type="date" name="from_custom" id="from_custom" value="{{ request('from') }}" min="1900-01-01" max="2099-12-31"
                    class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
            </div>
            <div id="customToWrap" class="{{ $rangeSel === 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">As on Date</label>
                <input type="date" name="to_custom" id="to_custom" value="{{ request('to') }}" min="1900-01-01" max="2099-12-31"
                    class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
            </div>

            <input type="hidden" name="from" id="from" value="{{ request('from') }}">
            <input type="hidden" name="to" id="to" value="{{ request('to') }}">

            <div class="flex gap-2 {{ $rangeSel === 'custom' ? '' : 'hidden' }}" id="searchBtn">
                <button type="submit"
                    class="rounded-md border border-gray-700 text-black dark:text-white  px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#22d3ee]
                                hover:shadow-[0_0_15px_#22d3ee]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);"
                                >Search</button>
                <a href="{{ route('reports.balance_sheet') }}"
                    class="rounded-md border border-gray-700 text-black dark:text-white px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">Reset</a>
            </div>
        </form>

        @if ($resp['success'] ?? false)
            {{-- DEBUG INFORMATION (Remove in production) --}}
           
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- LEFT COLUMN: DETAILED REPORT --}}
                <div class="space-y-6">
                    <!-- <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Balance Sheet Report</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Period: {{ $from }} to
                                {{ $to }}</p>
                            @if ($closingStockAmount > 0)
                                <p class="text-sm text-green-600 dark:text-green-400">
                                    <i class="fas fa-box mr-1"></i>Closing Stock as of {{ $to }}: ₹{{ $inr($closingStockAmount) }}
                                </p>
                            @endif
                        </div>
                    </div> -->
                    {{-- ASSETS (DR) --}}
                    <div
                        class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden  shadow-sm">
                        <div class="px-4 py-3 bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] backdrop-blur-md  border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-black-700 dark:text-gray-200">Assets</h2>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            {{-- Fixed Assets --}}
                            <?php $TotalDr = 0; ?>
                            @forelse ($drRows->where('strGroupName', 'Fixed Assets') as $r)
                                <?php $amount = (float) ($r->decMainAmount ?? 0); ?>
                                @if($amount > 0 )
                                    @php $amt = -1 * $r->decMainAmount; @endphp
                                @else 
                                    @php $amt = $displayAmountDr($r->decMainAmount ?? 0); @endphp
                                @endif
                                <a href="{{ route('reports.ledger', ['group_id' => $r->iPrimaryGroupId ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                    class="group block hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 transition-colors">
                                    <div class="flex items-center justify-between px-4 py-3">
                                        <span class="text-gray-900 dark:text-white group-hover:text-black">{{ $r->strGroupName ?? '-' }}</span>
                                        <span class="font-medium text-gray-900 dark:text-white group-hover:text-black">
                                            {{ $inr($amt) }}
                                            <?php $TotalDr+= $amt; ?>
                                        </span>
                                    </div>
                                </a>
                            @empty
                            @endforelse

                            {{-- Investments --}}
                            @forelse ($drRows->where('strGroupName', 'Investments') as $r)
                                <?php $amount = (float) ($r->decMainAmount ?? 0); ?>
                                @if($amount > 0 )
                                    @php $amt = -1 * $r->decMainAmount; @endphp
                                @else 
                                    @php $amt = $displayAmountDr($r->decMainAmount ?? 0); @endphp
                                @endif
                                <a href="{{ route('reports.ledger', ['group_id' => $r->iPrimaryGroupId ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                    class="group block hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 transition-colors">
                                    <div class="flex items-center justify-between px-4 py-3">
                                        <span class="text-gray-900 dark:text-white group-hover:text-black">{{ $r->strGroupName ?? '-' }}</span>
                                        <span class="font-medium text-gray-900 dark:text-white group-hover:text-black">
                                            {{ $inr($amt) }}
                                            <?php $TotalDr+= $amt; ?>
                                        </span>
                                    </div>
                                </a>
                            @empty
                            @endforelse

                            {{-- Current Assets (without closing stock) --}}
                            @if ($currentAssetsWithoutStock > 0)
                                <a href="{{ route('reports.ledger', ['group_id' => $iPrimaryGroupIdcurrentAsset, 'from' => request('from'), 'to' => request('to')]) }}"
                                    class="group block hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 transition-colors">
                                    <div class="flex items-center justify-between px-4 py-3">
                                        <span class="text-gray-900 dark:text-white group-hover:text-black">Current Assets</span>
                                        <span class="font-medium text-gray-900 dark:text-white group-hover:text-black">
                                            {{ $inr($currentAssetsWithoutStock) }}
                                            <?php $TotalDr+= $currentAssetsWithoutStock; ?>
                                        </span>
                                    </div>
                                </a>
                            @endif

                            {{-- Closing Stock - SEPARATE LINE ITEM --}}
                            @if ($closingStockAmount > 0)
                                <div class="block bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors border-l-4 border-green-500">
                                    <div class="flex items-center justify-between px-4 py-3">
                                        <span class="text-gray-900 dark:text-white group-hover:text-black flex items-center">
                                            <i class="fas fa-boxes text-green-600 dark:text-green-400 mr-2 text-sm"></i>
                                            <span class="font-medium">Closing Stock</span>
                                        </span>
                                        <span class="font-bold text-green-700 dark:text-green-300 text-lg">
                                            {{ $inr($closingStockAmount) }}
                                            <?php $TotalDr+= $closingStockAmount; ?>
                                        </span>
                                    </div>
                                    @if ($to)
                                        <div class="px-4 pb-2">
                                            <span class="text-xs text-green-600 dark:text-green-400">
                                                As of {{ $to }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Other Assets from groups --}}
                            
                            @foreach ($drRows as $r)
                                @if (!in_array($r->strGroupName, ['Fixed Assets', 'Investments', 'Current Assets']))
                                    <?php $amount = (float) ($r->decMainAmount ?? 0); ?>
                                    @if($amount > 0 )
                                        @php $amt = -1 * $r->decMainAmount; @endphp
                                    @else 
                                        @php $amt = $displayAmountDr($r->decMainAmount ?? 0); @endphp
                                    @endif
                                    @if(abs($amt) > 0)
                                        <a href="{{ route('reports.ledger', ['group_id' => $r->iPrimaryGroupId ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                            class="group block hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 transition-colors">
                                            <div class="flex items-center justify-between px-4 py-3">
                                                <span class="text-gray-900 dark:text-white group-hover:text-black">{{ $r->strGroupName ?? '-' }}</span>
                                                <span class="font-medium text-gray-900 dark:text-white group-hover:text-black">
                                                    {{ $inr($amt) }}
                                                    <?php $TotalDr+= $amt; ?>
                                                </span>
                                            </div>
                                        </a>
                                    @endif
                                @endif
                            @endforeach
                            @if ($showDiffOnAssetSide)
                                @if (abs($differenceAmount) > 0)
                                <div class="flex items-center justify-between px-4 py-3">
                                    <span class="text-gray-900 dark:text-white group-hover:text-black">
                                        Difference in Balance Sheet
                                    </span>

                                    <span class="font-medium text-gray-900 dark:text-white group-hover:text-black">
                                        {{ $inr($differenceAmount) }}
                                    </span>
                                </div>
                                @endif
                                @php
                                    $TotalDr += $differenceAmount;
                                @endphp
                            @endif
                            @if ($drRows->isEmpty() && $closingStockAmount == 0)
                                <div class="px-4 py-3 text-black-500 dark:text-gray-400 text-center">No asset rows.</div>
                            @endif
                            
                        </div>
                        <div
                            class="px-4 py-3 bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] backdrop-blur-md  border-t border-gray-200 dark:border-gray-700 flex justify-between">
                            <span class="font-semibold text-black-700 dark:text-gray-300">Total Assets</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">{{ $inr($TotalDr) }}</span>
                        </div>
                    </div>

                    {{-- LIABILITIES & EQUITY (CR) --}}
                    <div
                        class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden  shadow-sm">
                        <div class="px-4 py-3 bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] backdrop-blur-md  border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-black-700 dark:text-gray-200">Liabilities &amp; Equity
                            </h2>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            <?php $TotalCr = 0; ?>
                            @forelse ($crRows->where('strGroupName', 'Capital Account') as $r)
                                @php $amt = $displayAmount($r->decMainAmount ?? 0); @endphp
                                @if(abs($amt) > 0)
                                    <a href="{{ route('reports.ledger', ['group_id' => $r->iPrimaryGroupId ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                        class="group block hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 transition-colors">
                                        <div class="flex items-center justify-between px-4 py-3">
                                            <span class="text-gray-900 dark:text-white group-hover:text-black">{{ $r->strGroupName ?? '-' }}</span>
                                            <span class="font-medium text-gray-900 dark:text-white group-hover:text-black">
                                                {{ $inr($amt) }}
                                                <?php $TotalCr+= $amt; ?>
                                            </span>
                                        </div>
                                    </a>
                                @endif
                            @empty
                            @endforelse
                            @forelse ($crRows->where('strGroupName', 'Loans (Liability)') as $r)
                                @php $amt = $displayAmount($r->decMainAmount ?? 0); @endphp
                                @if(abs($amt) > 0)
                                    <a href="{{ route('reports.ledger', ['group_id' => $r->iPrimaryGroupId ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                        class="group block hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 transition-colors">
                                        <div class="flex items-center justify-between px-4 py-3">
                                            <span class="text-gray-900 dark:text-white group-hover:text-black">{{ $r->strGroupName ?? '-' }}</span>
                                            <span class="font-medium text-gray-900 dark:text-white group-hover:text-black">
                                                {{ $inr($amt) }}
                                                <?php $TotalCr+= $amt; ?>
                                            </span>
                                        </div>
                                    </a>
                                @endif
                            @empty
                            @endforelse
                            @forelse ($crRows->where('strGroupName', 'Current Liabilities') as $r)
                                @php $amt = $displayAmount($r->decMainAmount ?? 0); @endphp
                                @if(abs($amt) > 0)
                                <a href="{{ route('reports.ledger', ['group_id' => $r->iPrimaryGroupId ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                    class="group block hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 transition-colors">
                                    <div class="flex items-center justify-between px-4 py-3">
                                        <span class="text-gray-900 dark:text-white group-hover:text-black">{{ $r->strGroupName ?? '-' }}</span>
                                        <span class="font-medium text-gray-900 dark:text-white group-hover:text-black">
                                            {{ $inr($amt) }}
                                            <?php $TotalCr+= $amt; ?>
                                        </span>
                                    </div>
                                </a>
                                @endif
                            @empty
                            @endforelse
                            @forelse ($crRows->where('strGroupName', 'Suspense A/c') as $r)
                                @php $amt = $displayAmount($r->decMainAmount ?? 0); @endphp
                                @if(abs($amt) > 0)
                                <a href="{{ route('reports.ledger', ['group_id' => $r->iPrimaryGroupId ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                    class="group block hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 transition-colors">
                                    <div class="flex items-center justify-between px-4 py-3">
                                        <span class="text-gray-900 dark:text-white group-hover:text-black">{{ $r->strGroupName ?? '-' }}</span>
                                        <span class="font-medium text-gray-900 dark:text-white group-hover:text-black">
                                            {{ $inr($amt) }}
                                            <?php $TotalCr+= $amt; ?>
                                        </span>
                                    </div>
                                </a>
                                @endif
                            @empty
                            @endforelse

                            @forelse ($crRows as $r)
                                @if (!in_array($r->strGroupName, ['Capital Account','Loans (Liability)','Current Liabilities','Suspense A/c']))
                                    @php $amt = $displayAmount($r->decMainAmount ?? 0); @endphp
                                    @if(abs($amt) > 0)
                                    <a href="{{ route('reports.ledger', ['group_id' => $r->iPrimaryGroupId ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                        class="group block hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 transition-colors">
                                        <div class="flex items-center justify-between px-4 py-3">
                                            <span class="text-gray-900 dark:text-white group-hover:text-black">{{ $r->strGroupName ?? '-' }}</span>
                                            <span class="font-medium text-gray-900 dark:text-white group-hover:text-black">
                                                {{ $inr($amt) }}
                                                <?php $TotalCr+= $amt; ?>
                                            </span>
                                        </div>
                                    </a>
                                    @endif
                                @endif
                            @empty
                                <div class="px-4 py-3 text-black-500 dark:text-gray-400 text-center">No liability/equity
                                    rows.</div>
                            @endforelse
                            @if ($showDiffOnLiabilitySide)
                                @if (abs($differenceAmount) > 0)    
                                <div class="flex items-center justify-between px-4 py-3">
                                    <span class="text-gray-900 dark:text-white group-hover:text-black">
                                        Difference in Balance Sheet
                                    </span>

                                    <span class="font-medium text-gray-900 dark:text-white group-hover:text-black">
                                        {{ $inr($differenceAmount) }}
                                    </span>
                                </div>
                                @endif

                                @php
                                    $TotalCr += $differenceAmount;
                                @endphp
                            @endif
                        </div>
                        
                        <div
                            class="px-4 py-3 bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] backdrop-blur-md  border-t border-gray-200 dark:border-gray-700 space-y-2">
                            <!-- <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Liabilities</span>
                                <span class="font-bold text-gray-900 dark:text-gray-100">{{ $inr($liabs) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Equity</span>
                                <span class="font-bold text-gray-900 dark:text-gray-100">{{ $inr($equity) }}</span>
                            </div> -->
                            <div
                                class="flex items-center justify-between ">
                                <span class="font-semibold text-black-700 dark:text-gray-300">Total Liabilities & Equity </span>
                                <!-- <span
                                    class="font-bold text-gray-900 dark:text-gray-100">{{ $inr($liabs + $equity) }}</span> -->
                                    <span
                                    class="font-bold text-gray-900 dark:text-gray-100">{{ $inr($TotalCr) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: CHARTS & SUMMARY --}}
                <div class="space-y-6">
                    <h2 class="text-[#22d3ee] font-semibold dark:text-[#22d3ee] mb-3">Balance Sheet Analysis</h2>
                    {{-- PIE CHARTS SECTION --}}
                    <div class="space-y-6">
                        {{-- Assets vs Liabilities + Equity --}}
                        <!-- <div class=" rounded-xl shadow p-4">
                            <h3 class="font-semibold mb-3 text-center">Assets vs Liabilities + Equity</h3>
                            <div class="h-64">
                                <canvas id="balanceSheetChartOld"></canvas>
                            </div>
                            <div class="mt-3 text-center text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex flex-col space-y-1">
                                    <span class="flex items-center justify-center">
                                        <span class="inline-block w-3 h-3 bg-[#34d399] rounded-full mr-1"></span>
                                        Assets: ₹{{ number_format($assets, 2) }}
                                    </span>
                                    <span class="flex items-center justify-center">
                                        <span class="inline-block w-3 h-3 bg-[#22d3ee] rounded-full mr-1"></span>
                                        Liabilities: ₹{{ number_format($liabs, 2) }}
                                    </span>
                                    <span class="flex items-center justify-center">
                                        <span class="inline-block w-3 h-3 bg-[#a78bfa] rounded-full mr-1"></span>
                                        Equity: ₹{{ number_format($equity, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div> -->

                        <div class="flex items-center gap-10">
                            <div class="w-[300px] h-[300px]">
                                <canvas id="balanceSheetChart"></canvas>
                            </div>

                            <div id="bsLegend" class="flex-1"></div>
                        </div>

                        {{-- Breakdown Chart with Dropdown --}}
                        <!-- <div class=" rounded-xl shadow p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-center">Breakdown Analysis</h3>
                                <select id="breakdownType"
                                    class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                    <option value="assets">Assets Breakdown</option>
                                    <option value="liabilities">Liabilities Breakdown</option>
                                </select>
                            </div>
                            <div class="h-64">
                                <canvas id="breakdownChart"></canvas>
                            </div>
                            <div class="mt-3 text-center text-sm text-gray-600 dark:text-gray-400">
                                <div id="assetsLegend" class="flex flex-col space-y-1">
                                    <span class="flex items-center justify-center">
                                        <span class="inline-block w-3 h-3 bg-[#a78bfa] rounded-full mr-1"></span>
                                        Current Assets: ₹{{ number_format($currentAssetsWithoutStock, 2) }}
                                    </span>
                                    <span class="flex items-center justify-center">
                                        <span class="inline-block w-3 h-3 bg-[#34d399] rounded-full mr-1"></span>
                                        Closing Stock: ₹{{ number_format($closingStockDisplay, 2) }}
                                    </span>
                                    <span class="flex items-center justify-center">
                                        <span class="inline-block w-3 h-3 bg-[#fbbf24] rounded-full mr-1"></span>
                                        Fixed Assets: ₹{{ number_format($fixedAssets, 2) }}
                                    </span>
                                    <span class="flex items-center justify-center">
                                        <span class="inline-block w-3 h-3 bg-[#f472b6] rounded-full mr-1"></span>
                                        Investments: ₹{{ number_format($investments, 2) }}
                                    </span>
                                    <span class="flex items-center justify-center">
                                        <span class="inline-block w-3 h-3 bg-[#60a5fa] rounded-full mr-1"></span>
                                        Other Assets: ₹{{ number_format($otherAssets, 2) }}
                                    </span>
                                </div>
                                <div id="liabilitiesLegend" class="hidden flex-col space-y-1">
                                    <span class="flex items-center justify-center">
                                        <span class="inline-block w-3 h-3 bg-[#a78bfa] rounded-full mr-1"></span>
                                        Current Liabilities: ₹{{ number_format($currentLiabilities, 2) }}
                                    </span>
                                    <span class="flex items-center justify-center">
                                        <span class="inline-block w-3 h-3 bg-[#f472b6] rounded-full mr-1"></span>
                                        Long-term Liabilities: ₹{{ number_format($longTermLiabilities, 2) }}
                                    </span>
                                    <span class="flex items-center justify-center">
                                        <span class="inline-block w-3 h-3 bg-[#fbbf24] rounded-full mr-1"></span>
                                        Other Liabilities: ₹{{ number_format($otherLiabilities, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div> -->
                        <div class="flex items-center justify-between">
                            <h3 class="text-[#22d3ee] font-semibold dark:text-[#22d3ee] mb-3">Breakdown Analysis</h3>

                            <!-- <select id="breakdownType" class="glass-input bg-[rgba(10,20,35,0.6)] backdrop-blur-md border border-cyan-500/20 text-white rounded-lg px-3 py-2">
                                <option value="assets">Assets Breakdown</option>
                                <option value="liabilities">Liabilities Breakdown</option>
                            </select> -->
                            <div class="relative"
                                    x-data="{
                                        open: false,
                                        selected: 'assets',
                                        options: {
                                            'assets': 'Assets Breakdown',
                                            'liabilities': 'Liabilities Breakdown'
                                        },

                                        init() {
                                            this.$watch('selected', value => {
                                                updateBreakdown(value);
                                            });
                                        }
                                    }">

                                    <!-- Hidden input (optional if needed in form) -->
                                    <input type="hidden" id="breakdownType" :value="selected">

                                    <!-- Button -->
                                    <button type="button" @click="open = !open"
                                        class="w-full text-left
                                        bg-gradient-to-br from-white/60 to-white/30
                                        dark:from-white/10 dark:to-transparent
                                        backdrop-blur-xl
                                        border border-gray-300/80 dark:border-cyan-400/20
                                        text-gray-900 dark:text-white
                                        rounded-xl px-3 py-2 pr-10 text-sm
                                        focus:outline-none
                                        focus:ring-2 focus:ring-[#22d3ee]
                                        transition-all duration-300">

                                        <span x-text="options[selected]"></span>
                                    </button>

                                    <!-- Arrow -->
                                    <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-300">
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </div>

                                    <!-- Dropdown -->
                                    <ul x-show="open" @click.outside="open = false"
                                        x-transition
                                        class="absolute z-50 mt-2 w-full rounded-xl overflow-hidden
                                        bg-white/10 dark:bg-white/5
                                        backdrop-blur-2xl
                                        border border-white/20
                                        ring-1 ring-white/10
                                        shadow-[0_8px_40px_rgba(0,0,0,0.4)]">

                                        <!-- Assets -->
                                        <li>
                                            <button type="button"
                                                @click="selected = 'assets'; open = false"
                                                class="w-full text-left px-4 py-2 text-sm
                                                    text-gray-800 dark:text-white
                                                    hover:bg-black/10 dark:hover:bg-white/10
                                                    hover:text-[#22d3ee]"
                                                :class="selected === 'assets'
                                                    ? 'bg-[#22d3ee]/20 text-[#22d3ee]'
                                                    : ''">
                                                Assets Breakdown
                                            </button>
                                        </li>

                                        <!-- Liabilities -->
                                        <li>
                                            <button type="button"
                                                @click="selected = 'liabilities'; open = false"
                                                class="w-full text-left px-4 py-2 text-sm
                                                    text-gray-800 dark:text-white
                                                    hover:bg-black/10 dark:hover:bg-white/10
                                                    hover:text-[#22d3ee]"
                                                :class="selected === 'liabilities'
                                                    ? 'bg-[#22d3ee]/20 text-[#22d3ee]'
                                                    : ''">
                                                Liabilities Breakdown
                                            </button>
                                        </li>

                                    </ul>
                                </div>
                        </div>

                        <div class="flex items-center gap-10">
                            <div class="w-[300px] h-[300px]">
                                <canvas id="breakdownChart"></canvas>
                            </div>

                            <div id="breakdownLegend" class="flex-1"></div>
                        </div>
                    </div>

                    {{-- CLOSING STOCK SUMMARY CARD --}}
                    @if ($closingStockAmount > 0)
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-green-800 dark:text-green-300 flex items-center">
                                        <i class="fas fa-boxes mr-2"></i>
                                        Closing Stock Summary
                                    </h3>
                                    <p class="text-sm text-green-600 dark:text-green-400 mt-1">
                                        As of {{ $to }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-green-700 dark:text-green-300">
                                        ₹{{ $inr($closingStockAmount) }}
                                    </div>
                                    <div class="text-sm text-green-600 dark:text-green-400">
                                        {{ number_format(($closingStockAmount / $assets) * 100, 1) }}% of Total Assets
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SUMMARY CARDS --}}
            <!-- <div class="mt-6  grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="group">
                    <div class="border  border-gray-700 rounded-lg p-4
                    transition duration-1000 ease-in-out
                                    transition-property: all;
                                    group-hover:border-[#34d399]
                                    group-hover:shadow-[0_0_15px_#34d399]
                                    group-hover:scale-100
                                    group-hover:-translate-y-1"
                                    style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                        <div class="text-gray-500 group-hover:text-[#34d399] dark:group-hover:text-[#34d399] dark:text-gray-400 text-[12px] font-medium">Total Assets</div>
                        <div class="text-[16px] font-semiBold text-gray-900 dark:text-white">
                            ₹{{ number_format($assets, 2) }}
                        </div>
                    </div>
                </div>

                <div class="group">
                    <div class="border border-gray-700 rounded-lg p-4
                    transition duration-1000 ease-in-out
                                    transition-property: all;
                                    group-hover:border-[#22d3ee]
                                    group-hover:shadow-[0_0_15px_#22d3ee]
                                    group-hover:scale-100
                                    group-hover:-translate-y-1"
                                    style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                        <div class="text-gray-500 group-hover:text-[#22d3ee] dark:group-hover:text-[#22d3ee] dark:text-gray-400 text-[12px] font-medium">Total Liabilities</div>
                        <div class="text-[16px] font-semiBold text-gray-900 dark:text-white">
                            ₹{{ number_format($liabs, 2) }}
                        </div>
                    </div>
                </div>

                <div class="group">
                    <div
                        class="border border-gray-700 rounded-lg p-4
                       transition duration-1000 ease-in-out
                                    transition-property: all;
                                    group-hover:border-[#a78bfa]
                                    group-hover:shadow-[0_0_15px_#a78bfa]
                                    group-hover:scale-100
                                    group-hover:-translate-y-1"
                                    style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                        <div class="text-gray-500 group-hover:text-[#a78bfa] dark:group-hover:text-[#a78bfa] dark:text-gray-400 text-[12px] font-medium">Total Equity</div>
                        <div class="text-[16px] font-semiBold text-gray-900 dark:text-white">
                            ₹{{ number_format($equity, 2) }}
                        </div>
                    </div>
                </div>

                <div class="group">
                    <div
                        class="{{ $balanceDiff === 0.0 ? ' border-gray-700' : ' border-gray-700 ' }} border rounded-lg p-4
                        transition duration-1000 ease-in-out
                                    transition-property: all;
                                    group-hover:border-[#fbbf24]
                                    group-hover:shadow-[0_0_15px_#fbbf24]
                                    group-hover:scale-100
                                    group-hover:-translate-y-1"
                                    style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                        <div
                            class="{{ $balanceDiff === 0.0 ? 'text-gray-500 group-hover:text-[#fbbf24] dark:group-hover:text-[#fbbf24] dark:text-gray-400 text-[12px] font-medium' : 'text-gray-500 group-hover:text-[#fbbf24] dark:group-hover:text-[#fbbf24] dark:text-gray-400 text-[12px] font-medium' }} ">
                            Balance Status</div>
                        <div
                            class="text-[16px] font-semiBold {{ $balanceDiff === 0.0 ? 'text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white' }}">
                            {{ $balanceDiff === 0.0 ? 'Balanced' : 'Difference: ₹' . number_format(abs($balanceDiff), 2) }}
                        </div>
                    </div>
                </div>
            </div> -->

            {{-- Balance note --}}
            <!-- <div class="mt-4">
                @if ($balanceDiff === 0.0)
                    <div
                        class="rounded-md bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm text-green-800 dark:text-green-300">
                        Balanced: Dr equals Cr.
                    </div>
                @else
                    <div
                        class="rounded-md bg-amber-50 dark:bg-amber-900/20 px-4 py-3 text-sm text-amber-800 dark:text-amber-300">
                        Note: Dr and Cr differ by <strong>{{ $inr($balanceDiff) }}</strong>.
                    </div>
                @endif
            </div> -->
        @else
            <div class="mt-4 rounded-md bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-800 dark:text-red-300">
                {{ $resp['message'] ?? 'Failed to load' }}
            </div>
        @endif
    </div>
</div>

    {{-- Chart.js Library --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Initialize Pie Charts
        let breakdownChart; 
        const currentAssetsWithoutStock = {{ $currentAssetsWithoutStock ?? 0 }};
        const closingStock = {{ $closingStockDisplay ?? 0 }};
        const fixedAssets = {{ $fixedAssets ?? 0 }};
        const investments = {{ $investments ?? 0 }};
        const otherAssets = {{ $otherAssets ?? 0 }};

        const currentLiabilities = {{ $currentLiabilities ?? 0 }};
        const longTermLiabilities = {{ $longTermLiabilities ?? 0 }};
        const otherLiabilities = {{ $otherLiabilities ?? 0 }};
        const capitalAccount = {{ $capitalAccount ?? 0 }};
        document.addEventListener('DOMContentLoaded', function() {
            

            // Chart instances
            let balanceSheetChart;

            // Breakdown Chart
            const breakdownCtx = document.getElementById('breakdownChart').getContext('2d');
            const breakdowncolors = ['#a78bfa', '#34d399', '#fbbf24', '#f472b6', '#60a5fa'];
            // Initial breakdown chart (Assets Breakdown by default)
            breakdownChart = new Chart(breakdownCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Current Assets', 'Closing Stock', 'Fixed Assets', 'Investments', 'Other Assets'],
                    // datasets: [{
                    //     data: [currentAssetsWithoutStock, closingStock, fixedAssets, investments, otherAssets],
                    //     backgroundColor: ['#a78bfa', '#34d399', '#fbbf24', '#f472b6', '#60a5fa'],
                    //     borderColor: 'rgba(0,0,0,0.15)',
                    //     borderWidth: 1.5,
                    // }]
                    datasets: [{
                        data: [currentAssetsWithoutStock, closingStock, fixedAssets, investments, otherAssets],
                        backgroundColor: (ctx) => {
                            const chart = ctx.chart;
                            const { ctx: c, chartArea } = chart;
                            if (!chartArea) return;

                            return breakdowncolors.map(color => {
                                const gradient = c.createLinearGradient(
                                    0,
                                    chartArea.top,
                                    0,
                                    chartArea.bottom
                                );

                                gradient.addColorStop(0, color + 'FF');
                                gradient.addColorStop(0.4, color + 'AA');
                                gradient.addColorStop(0.7, color + '55');
                                gradient.addColorStop(1, color + '10');

                                return gradient;
                            });
                        },
                        borderColor: breakdowncolors, //'rgba(0,0,0,0.15)',
                        borderWidth: 1.5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const total = currentAssetsWithoutStock + closingStock + fixedAssets + investments + otherAssets;
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) :
                                        '0.0';
                                    return `${context.label}: ₹${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                },
                cutout: '70%',
                radius: '90%',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    elements: {
                        line: {
                            borderWidth: 3,
                            tension: 0.45
                        },
                        point: {
                            radius: 0,
                            hoverRadius: 6
                        }
                    },
                    cutout: '65%',
                    radius: '85%',
                    layout: {
                        padding: 10
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            renderLegend(
                'breakdownLegend',
                breakdownChart.data.labels,
                breakdownChart.data.datasets[0].data,
                breakdownChart.data.datasets[0].backgroundColor
            );
            
        });

        function updateBreakdown(type) {
            if (!breakdownChart) return;

            let colors, labels, data;

            if (type === 'assets') {
                labels = ['Current Assets', 'Closing Stock', 'Fixed Assets', 'Investments', 'Other Assets'];
                data = [currentAssetsWithoutStock, closingStock, fixedAssets, investments, otherAssets];
                colors = ['#a78bfa', '#34d399', '#fbbf24', '#f472b6', '#60a5fa'];
            } else {
                labels = ['Current Liabilities', 'Long-term Liabilities', 'Capital Account', 'Other Liabilities'];
                data = [currentLiabilities, longTermLiabilities, capitalAccount, otherLiabilities];
                colors = ['#a78bfa', '#34d399', '#fbbf24','#f472b6',];
            }

            // breakdownChart.data.labels = labels;
            // breakdownChart.data.datasets[0].data = data;
            const filtered = labels.map((label, i) => ({
                label,
                value: data[i],
                color: colors[i]
            })).filter(item => Number(item.value) > 0);

            breakdownChart.data.labels = filtered.map(x => x.label);
            breakdownChart.data.datasets[0].data = filtered.map(x => x.value);

            colors = filtered.map(x => x.color);

            // 🔥 APPLY SAME GRADIENT EFFECT
            breakdownChart.data.datasets[0].backgroundColor = (ctx) => {
                const chart = ctx.chart;
                const { ctx: c, chartArea } = chart;

                if (!chartArea) return;

                return colors.map(color => {
                    const gradient = c.createLinearGradient(
                        0,
                        chartArea.top,
                        0,
                        chartArea.bottom
                    );

                    gradient.addColorStop(0, color + 'FF');
                    gradient.addColorStop(0.4, color + 'AA');
                    gradient.addColorStop(0.7, color + '55');
                    gradient.addColorStop(1, color + '10');

                    return gradient;
                });
            };

            renderLegend(
                'breakdownLegend',
                labels,
                data,
                colors // legend still uses solid colors
            );

            breakdownChart.update();
        }

        let assets = Math.abs(Number({{ $assets ?? 0 }}));
        // Liabilities
        let liabilities = Number({{ $liabs }});
        // Equity (Capital)
        let equity = Number({{ $equity }});
        // Total for center
        let totalAssets = assets;
        console.log('Balance Sheet Data:', {
            assets,
            liabilities,
            equity
        });
        const colors = ['#22d3ee', '#fbbf24', '#a78bfa'];
        // ===== BALANCE SHEET PIE =====
        new Chart(document.getElementById('balanceSheetChart'), {
            type: 'doughnut',
            data: {
                labels: [
                    'Assets',
                    'Liabilities',
                    'Equity'
                ],
                datasets: [{
                    data: [
                        assets,
                        liabilities,
                        equity
                    ],
                    // backgroundColor: [
                    //     '#22d3ee', // assets (cyan)
                    //     '#fbbf24', // liabilities (yellow)
                    //     '#a78bfa' // equity (violet)
                    // ]
                    backgroundColor: (ctx) => {
                    const chart = ctx.chart;
                    const {
                        ctx: c,
                        chartArea
                    } = chart;
                    if (!chartArea) return;

                    

                    return colors.map(color => {
                        const gradient = c.createLinearGradient(0, chartArea.top, 0,
                            chartArea.bottom);

                        gradient.addColorStop(0, color + 'FF');
                        gradient.addColorStop(0.4, color + 'AA');
                        gradient.addColorStop(0.7, color + '55');
                        gradient.addColorStop(1, color + '10');

                        return gradient;
                    });
                },
                borderColor: colors, //'rgba(0,0,0,0.15)',
                    borderWidth: 1.5,
                }]
            },
            
            cutout: '70%',
            radius: '90%',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                elements: {
                    line: {
                        borderWidth: 3,
                        tension: 0.45
                    },
                    point: {
                        radius: 0,
                        hoverRadius: 6
                    }
                },
                cutout: '65%',
                radius: '85%',
                layout: {
                    padding: 10
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // let bsColors = ['#22d3ee', '#fbbf24', '#a78bfa'];
        let bsRaw = [
            { label: 'Assets', value: assets, color: '#22d3ee' },
            { label: 'Liabilities', value: liabilities, color: '#fbbf24' },
            { label: 'Equity', value: equity, color: '#a78bfa' }
        ];

        // ✅ REMOVE 0 VALUES
        bsRaw = bsRaw.filter(x => Number(x.value) > 0);

        let bsLabels = bsRaw.map(x => x.label);
        let bsValues = bsRaw.map(x => x.value);
        let bsColors = bsRaw.map(x => x.color);

        renderLegend('bsLegend', bsLabels, bsValues, bsColors);
        function renderLegend(containerId, labels, values, colors) {

            let html = '';

            labels.forEach((label, i) => {

                let value = Math.round(Number(values[i] || 0));
                if (value <= 0) return;
                html += `
            <div class="flex flex-col gap-1">

                <!-- LEFT -->
                <div class="flex items-center gap-2 min-w-0">
                    <span style="
                        width:10px;
                        height:10px;
                        border-radius:50%;
                        background:${colors[i]};
                        display:inline-block;
                    "></span>

                    <span class="truncate text-black-700 dark:text-gray-300">
                        ${label}
                    </span>
                </div>

                <!-- RIGHT -->
                <div class="pl-5 font-medium text-black dark:text-white">
                    ₹ ${value.toLocaleString('en-IN')}
                </div>

            </div>
            `;
            });

            document.getElementById(containerId).innerHTML = html;
        }


        function handleRangeChange(value) {

            const hidFrom = document.getElementById('from');
            const hidTo = document.getElementById('to');

            const financialYearRanges = @json($financialYearOptions->keyBy('value'));
            const selectedRange = financialYearRanges[value];

            const cfWrap = document.getElementById('customFromWrap');
            const ctWrap = document.getElementById('customToWrap');

            //const btnWrap = document.querySelector('.ml-auto');
            
            const pad = n => String(n).padStart(2, '0');
            const fmt = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

            function fyStartYearFor(date) {
                return date.getMonth() >= 3 ? date.getFullYear() : date.getFullYear() - 1;
            }

            function fyRange(startYr) {
                return {
                    from: new Date(startYr, 3, 1),
                    to: new Date(startYr + 1, 2, 31)
                };
            }

            function computeRange(kind) {
                const now = new Date();
                const fyStartYr = fyStartYearFor(now);

                if (kind === 'current_year') return fyRange(fyStartYr);
                if (kind === 'last_year') return fyRange(fyStartYr - 1);

                return null;
            }

            const isCustom = value === 'custom';

            // toggle UI
            cfWrap.classList.toggle('hidden', !isCustom);
            ctWrap.classList.toggle('hidden', !isCustom);
            const btnWrap = document.getElementById('searchBtn');

            
            // btnWrap.style.display = isCustom ? 'flex' : 'none';

            btnWrap.classList.toggle('hidden', !isCustom);
            if (!isCustom) {
                const r = selectedRange ? {
                    from: new Date(`${selectedRange.from}T00:00:00`),
                    to: new Date(`${selectedRange.to}T00:00:00`)
                } : computeRange(value);

                if (r) {
                    hidFrom.value = fmt(r.from);
                    hidTo.value = fmt(r.to);
                }

                // auto submit
                //document.querySelector('form').submit();
                document.getElementById('filterForm').submit();
            } else {
                hidFrom.value = '';
                hidTo.value = '';
            }
        }

        document.getElementById('filterForm').addEventListener('submit', function(e) {

            const selected = document.querySelector('input[name="range"]').value;

            if (selected === 'custom') {

                const toC = document.getElementById('to_custom');

                if (!toC.value) {
                    e.preventDefault();
                    showToast('Select As on Date','error');
                    return;
                }

                // Convert TO date
                const toDate = new Date(toC.value);

                // Financial year start logic
                let fyStartYear;

                // April = 3
                if (toDate.getMonth() >= 3) {
                    fyStartYear = toDate.getFullYear();
                } else {
                    fyStartYear = toDate.getFullYear() - 1;
                }

                // FY start date
                const fromDate = `${fyStartYear}-04-01`;

                // Pass hidden fields
                document.getElementById('from').value = fromDate;
                document.getElementById('to').value   = toC.value;
            }
        });

        function isValidDate(dateString) {

            const parts = dateString.split('/');

            if (parts.length !== 3) return false;

            const day = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10);
            const year = parseInt(parts[2], 10);

            // JS month starts from 0
            const date = new Date(year, month - 1, day);

            return (
                date.getFullYear() === year &&
                date.getMonth() === month - 1 &&
                date.getDate() === day
            );
        }
    </script>


@endsection