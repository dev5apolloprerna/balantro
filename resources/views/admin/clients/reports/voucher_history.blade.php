@extends('layouts.super_admin')
@section('title', 'Voucher History')

@section('content')
    @php
        // ---------- normalize ----------
        $payload = $data ?? ($resp['data'] ?? []);
        $rows = collect($rows ?? ($payload['rows'] ?? []));
        $meta = $resp['meta'] ?? [];
        $ledgerId = request('ledger_id');

        // ---------- helpers ----------
        $toFloat = function ($v) {
            if ($v === null || $v === '') {
                return 0.0;
            }
            return (float) str_replace(',', '', (string) $v);
        };

        // Indian format ##,##,###.00
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

        // Use values calculated by the service
        // $totalDr = $payload[0]['TotalDr'] ?? 0.0;
        // $totalCr = $payload[0]['TotalCr'] ?? 0.0;
        $totalDr = $toFloat($rows->first()->TotalDr ?? 0);
        $totalCr = $toFloat($rows->first()->TotalCr ?? 0);
        
         
        //$openingBalance = $openingBalanceData['balance'] ?? 0.0;
        // $openingSide = $openingBalanceData['side'] ?? 'Dr'; 
         
        $openingBalance = $toFloat($rows->first()->OpeningBalance ?? 0);
        $openingSide = $openingBalance >= 0 ? 'Dr' : 'Cr';
        $openingBalance = abs($openingBalance);

        // Get the last running balance for closing
        /* $lastRunningBalance = 0.0;
        if ($rows->isNotEmpty()) {
            $lastRow = $rows->last();
            $lastRunningBalance = $toFloat($lastRow->decRunningBalance ?? 0);
        }
        $closingBalance = abs($lastRunningBalance);
        $closingSide = $lastRunningBalance >= 0 ? 'Dr' : 'Cr';
        */


        $processedRows = [];
        $previousBalance = $openingSide === 'Dr' ? $openingBalance : -$openingBalance;
        // Add opening balance as first row
        $processedRows[] = (object) [
            'is_opening' => true,
            'strVchDate' => $from ? date('d-m-Y', strtotime($from)) : '',
            'vchNo' => 'OPENING BALANCE',
            'vchType' => 'Opening',
            'trnAccount' => 'Balance B/F',
            'DRAmount' => 0,
            'CRAmount' => 0,
            'opening_balance' => $previousBalance,
            'decRunningBalance' => $previousBalance,
            'side' => $openingSide,
        ];
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
        $rangeOptions = $financialYearOptions->pluck('label', 'value')->all();
        // Process actual voucher rows - use the running balance from database directly
        
        foreach ($rows as $r) {
            $drRaw = $toFloat($r->DRAmount ?? 0);
            $crRaw = $toFloat($r->CRAmount ?? 0);
            //$currentClosing = $toFloat($r->decRunningBalance ?? 0);
            
            $currentOpening = $previousBalance;
            // $currentClosing = $previousBalance + $drRaw - $crRaw;
            //$currentClosing = $previousBalance - $drRaw + $crRaw;
            $currentClosing = $previousBalance - abs($drRaw) + abs($crRaw);

            $side = $currentClosing >= 0 ? 'Dr' : 'Cr';
            

            $processedRows[] = (object) array_merge((array) $r, [
                'opening_balance' => $currentOpening,
                'decRunningBalance' => $currentClosing,
                'side' => $side,
            ]);

            $previousBalance = $currentClosing;
            
            //$processedRows[] = $processedRow;
        }
        $lastRunningBalance = $previousBalance;

        $closingBalance = abs($lastRunningBalance);

        $closingSide = $lastRunningBalance >= 0 ? 'Dr' : 'Cr';

        // Add closing balance as last row
        $processedRows[] = (object) [
            'is_closing' => true,
            'strVchDate' => $to ? date('d-m-Y', strtotime($to)) : now()->format('d-m-Y'),
            'vchNo' => 'CLOSING BALANCE',
            'vchType' => 'Closing',
            'trnAccount' => 'Balance C/F',
            'DRAmount' => 0,
            'CRAmount' => 0,
            'decRunningBalance' => $lastRunningBalance,
            'side' => $closingSide,
        ];

        $diff = abs($totalDr) - abs($totalCr);

        // header subtitle
        $periodText = function () use ($meta) {
            $f = date('d-m-Y', strtotime($meta['from'] ?? request('from')));
            $t = date('d-m-Y', strtotime($meta['to'] ?? request('to')));
            if ($f || $t) {
                return trim(($f ?: '—') . ' to ' . ($t ?: '—'));
            }
            return 'All time';
        };
    @endphp
    <style>
        /* ===== 6 COLOR SYSTEM ===== */

        .color-0 {
            --card: #22d3ee;
        }

        /* cyan */
        .color-1 {
            --card: #a78bfa;
        }

        /* violet */
        .color-2 {
            --card: #34d399;
        }

        /* mint */
        .color-3 {
            --card: #fbbf24;
        }

        /* yellow */
        .color-4 {
            --card: #f472b6;
        }

        /* pink */
        .color-5 {
            --card: #60a5fa;
        }

        /* blue */

        /* Smooth base transition */
        .card-hover>div {
            transition: all 0.3s ease;
        }

        /* ===== UPDATED HOVER EFFECT ===== */
        .card-hover:hover>div {
            /* softer shadow from all sides */
            box-shadow:
                0 8px 20px rgba(0, 0, 0, 0.06),
                0 0 12px var(--card);

            transform: translateY(-3px);

            /* 🔥 border color change */
            border-color: var(--card) !important;
        }

        /* ===== LABEL COLOR CHANGE ===== */
        .card-hover:hover .text-\[12px\] {
            color: var(--card) !important;
        }
    </style>
    <div class="mt-1 border-b border-gray-200 dark:border-gray-700 pb-1">
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
                    {{ $labelFY ?? '' }}
                </span> -->
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
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ strtoupper($ledgerName ?? '') }}</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    • {{ $periodText() }}
                </p>
            </div>
            @php
                $queryParams = array_merge(request()->query(), [
                    'ledger_id' => request('ledger_id', $ledgerId ?? ''),
                    'from' => request('from', $from ?? ''),
                    'to' => request('to', $to ?? ''),
                    'range' => request('range', $rangeSel ?? ''),
                    'guid' => $guid,
                ]);
            @endphp
           
            <div>
                <a href="{{ route('reports.voucher-history.export-pdf', $queryParams) }}"
                    class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700
                                transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1">
                    <i class="fas fa-file-pdf mr-1"></i>
                </a>
                &nbsp;
                <a href="{{ route('reports.voucher-history.export-excel', $queryParams) }}"
                    class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700
                                transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#34d399]
                                hover:shadow-[0_0_15px_#34d399]
                                hover:scale-105
                                hover:-translate-y-1">
                    <i class="fas fa-file-excel mr-1"></i>
                </a>
            </div>
        </div>

        {{-- Filters (GET for shareable URLs) --}}
        <form method="GET" action="{{ route('reports.voucher_history') }}" id="filterForm"
            class="mt-2 rounded-lg p-2 flex flex-wrap items-end gap-3">
            <div style="display: none">
                <label class="block text-xs text-black-900 dark:text-gray-300 mb-1">Party GUID</label>
                <input type="text" name="partyguid" value="{{ request('partyguid') }}"
                    class="w-64 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
            </div>

            <div style="display: none">
                <label class="block text-xs text-black-900 dark:text-gray-300 mb-1">Ledger ID</label>
                <input type="number" name="ledger_id" value="{{ request('ledger_id') }}"
                    class="w-32 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
            </div>
            
            <div>
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

                    <label class="block text-xs text-black-900 dark:text-gray-300 mb-1">Date Range</label>

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
                        focus:ring-2 focus:ring-[#22d3ee]">

                        <span x-text="options[selected] || 'Select Year'"></span>
                    </button>

                    <!-- Arrow -->
                    <div class="mt-2 pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>

                    <!-- Dropdown -->
                    <ul x-show="open" @click.outside="open = false"
                        x-transition
                        class="absolute z-50 mt-2 w-full rounded-xl overflow-hidden
                        bg-white/10 dark:bg-white/5 backdrop-blur-2xl border border-white/20">

                        @forelse ($financialYearOptions as $financialYear)
                            <li>
                                <button type="button"
                                    @click="selected=@js($financialYear['value']); open=false"
                                    class="w-full px-4 py-2 text-left hover:text-[#22d3ee]">
                                    {{ $financialYear['label'] }}
                                </button>
                            </li>
                        @empty
                            <li class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">No financial years found</li>
                        @endforelse
                        <li>
                            <button type="button"
                                @click="selected='custom'; open=false"
                                class="w-full px-4 py-2 text-left hover:text-[#22d3ee]">
                                Custom Date
                            </button>
                        </li>

                    </ul>
                </div>
            </div>

            {{-- Custom date inputs (shown only when range=custom) --}}
            <div id="customFromWrap" class="{{ $rangeSel === 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs text-black-900  dark:text-gray-300 mb-1">From Date</label>
                <input type="date" name="from_custom" id="from_custom" value="{{ request('from') }}" min="1900-01-01"
                max="2099-12-31"
                    class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
            </div>
            <div id="customToLabel"
                class="pb-2 text-gray-500 dark:text-gray-400 {{ $rangeSel === 'custom' ? '' : 'hidden' }}">TO</div>
            <div id="customToWrap" class="{{ $rangeSel === 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs text-black-900  dark:text-gray-300 mb-1">To Date</label>
                <input type="date" name="to_custom" id="to_custom" value="{{ request('to') }}" min="1900-01-01"
                    max="2099-12-31"
                    class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
            </div>

            {{-- Hidden fields actually submitted for presets (and also for custom via JS before submit) --}}
            <input type="hidden" name="from" id="from" value="{{ request('from') }}">
            <input type="hidden" name="to" id="to" value="{{ request('to') }}">

            <div class="ml-auto" style="display: none">
                <label class="block text-xs text-black-900  dark:text-gray-300 mb-1">Search</label>
                <input id="vhSearch" type="text" placeholder="Voucher no / account / type…"
                    class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300" />
            </div>

            <div class="flex gap-2">
                <button id="searchBtn" class="rounded-md border border-gray-700 text-black dark:text-white  px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#22d3ee]
                                hover:shadow-[0_0_15px_#22d3ee]
                                hover:scale-105
                                hover:-translate-y-1">Search</button>
                <a id="resetBtn" href="{{ route('reports.voucher_history', ['ledger_id' => request('ledger_id'), 'partyguid' => request('partyguid')]) }}"
                    class="rounded-md border border-gray-700 text-black dark:text-white px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1">Reset</a>
            </div>
        </form>

         {{-- Balance Summary Cards - All in one row --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Opening Balance --}}
            <div class="group card-hover color-0">
                <div class="relative  rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[92px] transition-all duration-300 hover:shadow-md">
                    <div class="p-4 pl-6">
                        <div class="flex items-start justify-between">
                            <div class="pr-3 flex-1">
                                <div class="text-[12px] uppercase tracking-wide text-black-500 dark:text-gray-400 truncate">
                                    Opening Balance
                                </div>
                                <div class="mt-0.5 text-xl md:text-2xl font-semibold leading-tight text-black-900 dark:text-white tabular-nums" style="font-size: 1rem !important;">
                                    ₹ {{ $inr($openingBalance) }} ({{ $openingSide }})
                                </div>
                            </div>

                            <!-- <div class="shrink-0">
                                <div class="h-9 w-9 md:h-10 md:w-10 rounded-full flex items-center justify-center bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300 transition-colors group-hover:bg-opacity-80">
                                    <i class="fa-solid fa-building-columns text-sm md:text-base"></i>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
            

            <div class="group card-hover color-1">
                <div class="relative  rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[92px] transition-all duration-300 hover:shadow-md">
                    <div class="p-4 pl-6">
                        <div class="flex items-start justify-between">
                            <div class="pr-3 flex-1">
                                <div class="text-[12px] uppercase tracking-wide text-black-500 dark:text-gray-400 truncate">
                                    Total Debit
                                </div>
                                <div class="mt-0.5 text-xl md:text-2xl font-semibold leading-tight text-black-900 dark:text-white tabular-nums" style="font-size: 1rem !important;">
                                    ₹ {{ $inr(abs($totalDr)) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Total Credit --}}
            <div class="group card-hover color-2">
                <div class="relative  rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[92px] transition-all duration-300 hover:shadow-md">
                    <div class="p-4 pl-6">
                        <div class="flex items-start justify-between">
                            <div class="pr-3 flex-1">
                                <div class="text-[12px] uppercase tracking-wide text-black-500 dark:text-gray-400 truncate">
                                    Total Credit
                                </div>
                                <div class="mt-0.5 text-xl md:text-2xl font-semibold leading-tight text-black-900 dark:text-white tabular-nums" style="font-size: 1rem !important;">
                                    ₹ {{ $inr($totalCr) }}
                                </div>
                            </div>
                            <!-- <div class="shrink-0">
                                <div class="h-9 w-9 md:h-10 md:w-10 rounded-full flex items-center justify-center bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300 transition-colors group-hover:bg-opacity-80">
                                    <i class="fa-solid fa-money-bill-wave text-sm md:text-base"></i>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>

            {{-- Closing Balance --}}
            
            <div class="group card-hover color-3">
                <div class="relative  rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[92px] transition-all duration-300 hover:shadow-md">
                    <div class="p-4 pl-6">
                        <div class="flex items-start justify-between">
                            <div class="pr-3 flex-1">
                                <div class="text-[12px] uppercase tracking-wide text-black-500 dark:text-gray-400 truncate">
                                    Closing Balance
                                </div>
                                <div class="mt-0.5 text-xl md:text-2xl font-semibold leading-tight text-black-900 dark:text-white tabular-nums" style="font-size: 1rem !important;">
                                    ₹ {{ $inr($closingBalance) }} ({{ $closingSide }})
                                </div>
                            </div>
                            <!-- <div class="shrink-0">
                                <div class="h-9 w-9 md:h-10 md:w-10 rounded-full flex items-center justify-center bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-300 transition-colors group-hover:bg-opacity-80">
                                    <i class="fa-solid fa-cube text-sm md:text-base"></i>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="mt-5 overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden group-block">
            <table class="min-w-full text-sm text-left ">
                <thead class="bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] dark:bg-gray-900/40 sticky top-0 z-10">
                    <tr class="text-black-900 dark:text-gray-300">
                        <th class="px-4 py-2 font-bold">Date</th>
                        <th class="px-4 py-2 font-bold">Voucher No</th>
                        <th class="px-4 py-2 font-bold">Type</th>
                        <th class="px-4 py-2 font-bold">Account</th>
                        <th class="px-4 py-2 font-bold text-right">Opening</th>
                        <th class="px-4 py-2 font-bold text-right">Debit</th>
                        <th class="px-4 py-2 font-bold text-right">Credit</th>
                        <th class="px-4 py-2 font-bold text-right">Closing</th>
                        <!-- <th class="px-4 py-2 font-semibold text-center">Side</th> -->
                    </tr>
                </thead>
                <tbody id="vhBody" class="divide-y divide-gray-100 dark:divide-gray-800 tabular-nums">
                    @forelse ($processedRows as $r)
                        @php
                            $date = $r->strVchDate ?? ($r->VchDate ?? now()->format('d-m-Y'));
                            $vno = $r->vchNo ?? ($r->VoucherNo ?? '-');
                            $type = $r->vchType ?? ($r->VoucherType ?? '-');
                            $acc = $r->trnAccount ?? ($r->Particulars ?? '-');

                            $drRaw = $toFloat($r->DRAmount ?? ($r->DrAmount ?? 0));
                            $crRaw = $toFloat($r->CRAmount ?? ($r->CrAmount ?? 0));

                            // For display, show absolute values
                            $drDisp = abs($drRaw);
                            $crDisp = abs($crRaw);

                            // Get closing balance (running balance from database)
                            $closing = $toFloat($r->decRunningBalance ?? 0);

                            // Determine side based on closing balance
                            $side = $r->side ?? ($closing >= 0 ? 'Dr' : 'Cr');

                            // CORRECTED: Special styling for opening/closing rows
                            $isOpening = $r->is_opening ?? false;
                            $isClosing = $r->is_closing ?? false;
                            $isSpecialRow = $isOpening || $isClosing;

                            // $rowClass = $isSpecialRow ? 'bg-blue-50 dark:bg-blue-900/20 font-semibold' : 'odd:bg-white even:bg-gray-50 dark:odd:bg-gray-900 dark:even:bg-gray-900/40 hover:bg-gray-100 dark:hover:bg-gray-800';
                        @endphp
                        @php

                            $voucherUrl = null;

                            if (
                                !$isSpecialRow &&
                                !empty($r->strGUID) &&
                                !empty($r->vchType)
                            ) {
                                $voucherUrl = route(
                                    'clients.reports.voucher-history.viewVoucher',
                                    [
                                        'guid' => $guid,
                                        'strGUID' => urlencode($r->strGUID),
                                        'vchType' => urlencode($r->vchType)
                                    ]
                                );
                            }

                        @endphp
                        <!-- hover:bg-transparent -->
                        <tr class="group  hover:backdrop-blur-md hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] transition-all duration-300 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80">
                            <td class="px-4 py-2 group-hover:text-black">
                                 @if(!$isSpecialRow)
                                    <a href="{{ $voucherUrl }}"
                                    class="text-blue-600 hover:underline group-hover:text-black">
                                        <div class="text-gray-900 dark:text-gray-100 group-hover:text-black">
                                        {{ $isSpecialRow ? $date : \Carbon\Carbon::parse($date)->format('d-m-Y') ?? $date }}
                                     </div>
                                    </a>
                                @else
                                    {{ $isSpecialRow ? $date : \Carbon\Carbon::parse($date)->format('d-m-Y') ?? $date }}
                                @endif
                            </td>
                            <td class="px-4 py-2 group-hover:text-black voucher-no">
                                <!-- {{ $vno }} -->
                                  @if(!$isSpecialRow)
                                    <a href="{{ $voucherUrl }}"
                                    class="text-blue-600 hover:underline group-hover:text-black">
                                        <div class="text-gray-900 dark:text-gray-100 group-hover:text-black">
                                            {{ $vno }}
                                        </div>
                                    </a>
                                @else
                                    {{ $vno }}
                                @endif
                            </td>
                            <td class="px-4 py-2 group-hover:text-black voucher-type">
                                @if(!$isSpecialRow)
                                    <a href="{{ $voucherUrl }}"
                                    class="text-blue-600 hover:underline group-hover:text-black">
                                        <div class="text-gray-900 dark:text-gray-100 group-hover:text-black">
                                            {{ $type }}
                                        </div>
                                    </a>
                                 @else
                                    {{ $type }}
                                @endif
                            </td>
                            <td class="px-4 py-2 group-hover:text-black voucher-acc">
                                 @if(!$isSpecialRow)
                                    <a href="{{ $voucherUrl }}"
                                    class="text-blue-600 hover:underline group-hover:text-black">
                                        <div class="text-gray-900 dark:text-gray-100 group-hover:text-black">
                                        {{ $acc }}
                                         </div>
                                    </a>
                                @else
                                    {{ $acc }}
                                @endif
                                    </td>
                            {{-- Opening Balance Column --}}
                            <td
                                class="px-4 py-2 group-hover:text-black  text-right text-black-700 dark:text-gray-300 ">
                                {{ $inr(abs($r->opening_balance ?? ($currentOpening ?? 0))) }}
                            </td>
                            {{-- Dr Column --}}
                            <td
                                class="px-4 py-2 group-hover:text-black  text-right text-black-700 dark:text-gray-300 ">
                                {{ $drDisp > 0 && !$isSpecialRow ? $inr($drDisp) : '0.00' }}
                            </td>

                            {{-- Cr Column --}}
                            <td
                                class="px-4 py-2 group-hover:text-black  text-right text-black-700 dark:text-gray-300 ">
                                {{ $crDisp > 0 && !$isSpecialRow ? $inr($crDisp) : '0.00' }}
                            </td>

                            {{-- Closing Balance Column --}}
                            <td
                                class="px-4 py-2 group-hover:text-black  text-right text-black-700 dark:text-gray-300 ">
                                {{ $inr(abs($closing)) }}
                            </td>

                            {{-- Side Column --}}
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-3 text-sm text-black-900 dark:text-gray-300">No vouchers
                                found.</td>
                        </tr>
                    @endforelse
                </tbody>
                
            </table>
        </div>
    </div>
</div>
    {{-- Rest of your JavaScript code remains the same --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const q = document.getElementById('vhSearch');
            if (!q) return;
            q.addEventListener('input', function() {
                const needle = (q.value || '').toLowerCase();
                document.querySelectorAll('#vhBody tr').forEach(tr => {
                    const txt = (
                        (tr.querySelector('.voucher-no')?.textContent || '') + ' ' +
                        (tr.querySelector('.voucher-type')?.textContent || '') + ' ' +
                        (tr.querySelector('.voucher-acc')?.textContent || '')
                    ).toLowerCase();
                    tr.style.display = txt.includes(needle) ? '' : 'none';
                });
            });
        });
    </script>

    {{-- Your existing date range JavaScript code --}}
    <script>
        function handleRangeChange(value) {
            const financialYearRanges = @json($financialYearOptions->keyBy('value'));
            const selectedRange = financialYearRanges[value];
            const isCustom = value === 'custom';

            document.getElementById('customFromWrap')?.classList.toggle('hidden', !isCustom);
            document.getElementById('customToLabel')?.classList.toggle('hidden', !isCustom);
            document.getElementById('customToWrap')?.classList.toggle('hidden', !isCustom);

            if (isCustom) {
                document.getElementById('from').value = '';
                document.getElementById('to').value = '';
                return;
            }

            if (! selectedRange) {
                return;
            }

            document.getElementById('from').value = selectedRange.from;
            document.getElementById('to').value = selectedRange.to;
            document.getElementById('filterForm').submit();
        }
    </script>
@endsection
