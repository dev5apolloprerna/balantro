@extends('layouts.super_admin')
@section('title', 'All Ledger')

@section('content')
    @php
        $rows = collect($rows ?? ($data['rows'] ?? ($resp['data']['rows'] ?? [])));
        $meta = $resp['meta'] ?? [];
        // Helpers
        $toFloat = function ($v) {
            if ($v === null || $v === '') {
                return 0.0;
            }
            return (float) str_replace(',', '', (string) $v);
        };
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
        
        $sideByClosing = function ($row) use ($toFloat) {
            $dr = $toFloat($row->decDr ?? 0);
            $cr = $toFloat($row->decCr ?? 0);

            if ($dr > $cr) {
                return 'Dr';
            } elseif ($cr > $dr) {
                return 'Cr';
            }

            // fallback: check closing balance
            $cl = $toFloat($row->decClBl ?? 0);
            return $cl < 0 ? 'Cr' : 'Dr';
        };

        // Totals
        // $totalDr = $rows->sum(fn($r) => $toFloat($r->decDr ?? 0));
        // $totalCr = $rows->sum(fn($r) => $toFloat($r->decCr ?? 0));
        
        $totalDr = $rows->sum(fn($r) => abs($toFloat($r->decDr ?? 0)));
        $totalCr = $rows->sum(fn($r) => abs($toFloat($r->decCr ?? 0)));

        $net = $totalDr - $totalCr;

        $totalOp = $rows->sum(fn($r) => $toFloat($r->decOpBl ?? 0));
        $totalCl = $rows->sum(fn($r) => $toFloat($r->decClBl ?? 0));
        // Grouping
        $byParent = $rows->groupBy('strParents');
        $periodText = function () use ($meta) {
            $f = date('d-m-Y', strtotime($meta['from'] ?? request('from')));
            $t = date('d-m-Y', strtotime($meta['to'] ?? request('to')));
            if ($f || $t) {
                return trim(($f ?: '—') . ' to ' . ($t ?: '—'));
            }
            return 'All time';
        };
    @endphp

    <div class="container py-3">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">All Ledger</h1>
                <p class="text-xs text-black-500 dark:text-gray-400 mt-0.5">
                        • {{ $periodText() }}
                </p>
            </div>
            @php
                $queryParams = array_merge(request()->query(), [
                    'groupId' => request('groupId', $groupId ?? ''),
                    'strCustomerName' => request('strCustomerName', $strCustomerName ?? ''),
                    'from' => request('from', $from ?? ''),
                    'to' => request('to', $to ?? ''),
                    'range' => request('range', $rangeSel ?? ''),
                ]);
                
            @endphp
            <div>
                <a href="{{ route('reports.ledger.export-pdf', $queryParams) }}" title="Export into PDF"
                    class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700
                                transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                    <i class="fas fa-file-pdf"></i>
                </a>
                &nbsp;
                <a href="{{ route('reports.ledger.export-excel', $queryParams) }}" title="Export into Excel"
                    class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700
                                transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#34d399]
                                hover:shadow-[0_0_15px_#34d399]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">
                    <i class="fas fa-file-excel"></i>
                </a>
            </div>
        </div>
        
        {{-- Auto-search form --}}
        <form method="POST" action="{{ route('reports.ledger') }}" id="searchForm"
            class="mt-2 rounded-lg p-2 flex flex-wrap items-end gap-3">
            @csrf
            @php
                
            @endphp
            
            {{-- Group Filter --}}
            <div>
                <div class="relative"
                    x-data="{
                        open: false,
                        selected: '{{ request('group_id') ?? '' }}',
                        label: '{{ collect($GroupMasters)->firstWhere('iGroupId', request('group_id'))->strGroupName ?? 'Select Group' }}'
                    }">

                    <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Group</label>

                    <!-- Hidden -->
                    <input type="hidden" name="group_id" :value="selected">

                    <!-- Button -->
                    <button type="button" @click="open = !open"
                        class="w-full text-left
                        bg-gradient-to-br from-white/60 to-white/30
                        dark:from-white/10 dark:to-transparent
                        backdrop-blur-xl
                        border border-gray-300/80 dark:border-cyan-400/20
                        text-gray-900 dark:text-white
                        rounded-xl px-3 py-2 pr-10 text-sm">

                        <span x-text="label"></span>
                    </button>

                    <!-- Arrow -->
                    <div class="mt-2 pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>

                    <!-- Dropdown -->
                    <ul x-show="open" @click.outside="open = false"
                        class="absolute z-50 mt-2 w-full rounded-xl overflow-auto max-h-60
                        bg-white/10 dark:bg-white/5 backdrop-blur-2xl border border-white/20">

                        <!-- Default -->
                        <li>
                            <!-- @click="selected=''; label='Select Group'; open=false" -->
                            <button type="button"
                               @click="selected='';
                                    label='Select Group';
                                    open=false;
                                    setTimeout(() => {
                                        autoSubmitIfPresetRange();
                                    }, 100);"
                                class="w-full text-left px-4 py-2 text-sm hover:text-[#22d3ee]">
                                Select Group
                            </button>
                        </li>

                        @foreach ($GroupMasters as $Group)
                            <li>
                                <!-- @click="selected='{{ $Group->iGroupId }}'; label='{{ $Group->strGroupName }}'; open=false" -->
                                <button type="button"
                                    @click="
                                        selected='{{ $Group->iGroupId }}';
                                        label='{{ $Group->strGroupName }}';
                                        open=false;
                                        setTimeout(() => {
                                            autoSubmitIfPresetRange();
                                        }, 100);"
                                    class="w-full text-left px-4 py-2 text-sm hover:text-[#22d3ee]">
                                    {{ $Group->strGroupName }}
                                </button>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>
            
            {{-- Ledger Name Filter --}}
            <div>
                <label class="block text-xs text-black-600 dark:text-gray-300 mb-1">Ledger</label>
                <input name="strCustomerName" id="strCustomerName" oninput="
                        clearTimeout(window.ledgerTimer);
                        const range = document.querySelector('input[name=\'range\']').value;
                        if(range !== 'custom') {
                            window.ledgerTimer = setTimeout(() => {
                                document.getElementById('searchForm').submit();
                            }, 600);
                        }" value="{{ request('strCustomerName') }}"
                    placeholder="Search Ledger..."
                    class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
            </div>
            
            {{-- Date Range Filter --}}
            <div>
                <div class="relative"
                    x-data="{
                        open: false,
                        selected: '{{ $rangeSel }}',
                        options: @js(collect($financialYears ?? [])->mapWithKeys(fn ($year) => [(string) $year->iYearId => $year->strYear])->put('custom', 'Custom Date')->all()),
                        init() {
                            this.$watch('selected', value => {
                                handleRangeChange(value);
                            });
                        }
                    }">
                    <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Date Range</label>
                    <input type="hidden" name="range" :value="selected">
                    <!-- Button -->
                    <button type="button" @click="open = !open"
                        class="w-full text-left
                        bg-gradient-to-br from-white/60 to-white/30
                        dark:from-white/10 dark:to-transparent
                        backdrop-blur-xl
                        border border-gray-300/80 dark:border-cyan-400/20
                        text-gray-900 dark:text-white
                        rounded-xl px-3 py-2 pr-10 text-sm">

                        <span x-text="options[selected] ?? 'Select Range'"></span>
                    </button>

                    <!-- Arrow -->
                    <div class="mt-2 pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>

                    <!-- Dropdown -->
                    <ul x-show="open" @click.outside="open = false"
                        class="absolute z-50 mt-2 w-full rounded-xl overflow-hidden
                        bg-white/10 dark:bg-white/5 backdrop-blur-2xl border border-white/20">

                        @forelse ($financialYears ?? [] as $financialYear)
                            <li>
                                <button type="button"
                                    @click="selected='{{ $financialYear->iYearId }}'; open=false"
                                    class="w-full px-4 py-2 text-left hover:text-[#22d3ee]">
                                    {{ $financialYear->strYear }}
                                </button>
                            </li>
                        @empty
                            <li>
                                <span class="block px-4 py-2 text-sm text-gray-500 dark:text-gray-300">No financial years found</span>
                            </li>
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

            {{-- Custom date inputs --}}
            <div id="customFromWrap" class="{{ $rangeSel === 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs text-black-600 dark:text-gray-300 mb-1">From Date</label>
                <input type="date" name="from_custom" id="from_custom" value="{{ request('from') }}" min="1900-01-01"
                    max="2099-12-31"
                    class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
            </div>
            <div id="customToLabel"
                class="pb-2 text-black-500 dark:text-gray-400 {{ $rangeSel === 'custom' ? '' : 'hidden' }}">TO</div>
            <div id="customToWrap" class="{{ $rangeSel === 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs text-black-600 dark:text-gray-300 mb-1">To Date</label>
                <input type="date" name="to_custom" id="to_custom" value="{{ request('to') }}" min="1900-01-01"
                    max="2099-12-31"
                    class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
            </div>

            {{-- Hidden fields --}}
            <input type="hidden" name="from" id="from" value="{{ request('from') }}">
            <input type="hidden" name="to" id="to" value="{{ request('to') }}">

            {{-- Manual search buttons for custom date range --}}
            <div id="customSearchButtons" class="{{ $rangeSel === 'custom' ? '' : 'hidden' }} flex gap-2">
                <button type="submit"
                    class="rounded-md border border-gray-700 text-black dark:text-white  px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#22d3ee]
                                hover:shadow-[0_0_15px_#22d3ee]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">Search</button>
                <a href="{{ route('reports.ledger') }}"
                    class="rounded-md border border-gray-700 text-black dark:text-white px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">Reset</a>
            </div>
        </form>

        {{-- Rest of your content remains the same --}}
        {{-- <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 ">
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Ledgers</div>
                <div class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $rows->count() }}</div>
            </div>
        </div> --}}

        {{-- Grouped tables --}}
        <div class="mt-2 space-y-5" id="ledgerGroups">
            @forelse($byParent as $parent => $list)
                
                <!-- @php
                    $gDr = $list->sum(fn($r) => $toFloat($r->decDr ?? 0));
                    $gCr = $list->sum(fn($r) => $toFloat($r->decCr ?? 0));
                @endphp -->
                @php
                    // ✅ Remove zero-balance rows
                    $filteredList = $list->filter(function ($r) use ($toFloat) {
                        $op = $toFloat($r->decOpBl ?? 0);
                        $dr = $toFloat($r->decDr ?? 0);
                        $cr = $toFloat($r->decCr ?? 0);
                        $cl = $toFloat($r->decClBl ?? 0);

                        return !($op == 0 && $dr == 0 && $cr == 0 && $cl == 0);
                    });

                    // Recalculate totals after filter
                    // $gDr = $filteredList->sum(fn($r) => $toFloat($r->decDr ?? 0));
                    // $gCr = $filteredList->sum(fn($r) => $toFloat($r->decCr ?? 0));
                    
                    $gDr = $filteredList->sum(fn($r) => abs($toFloat($r->decDr ?? 0)));
                    $gCr = $filteredList->sum(fn($r) => abs($toFloat($r->decCr ?? 0)));
                @endphp
                @if ($filteredList->count() > 0)

                <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden group-block">
                    <div
                        class="px-4 py-3  border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="text-sm font-semibold text-black-700 dark:text-gray-200">
                            {{ $parent ?: 'Ungrouped' }} 
                            <!-- — <span class="font-normal text-gray-500 dark:text-gray-400">{{ $list->count() }} ledgers</span> -->
                        </div>
                        <div class="text-xs md:text-sm text-black-600 dark:text-gray-300">
                            <!-- <span class="mr-4">Dr: <strong
                                    class="text-gray-600 dark:text-gray-300">{{ $inr($gDr) }}</strong></span>
                            <span>Cr: <strong class="text-gray-600 dark:text-gray-300">{{ $inr($gCr) }}</strong></span> -->
                            @php
                                $gOp = $filteredList->sum(fn($r) => $toFloat($r->decOpBl ?? 0));
                                $gCl = $filteredList->sum(fn($r) => $toFloat($r->decClBl ?? 0));
                            @endphp

                            <!-- <span>Op:
                                <strong>
                                    {{ $inr(abs($gOp)) }} {{ $gOp < 0 ? 'Cr' : 'Dr' }}
                                </strong>
                            </span> |

                            <span>Dr:
                                <strong>{{ $inr($gDr) }}</strong>
                            </span> |

                            <span>Cr:
                                <strong>{{ $inr($gCr) }}</strong>
                            </span> |

                            <span>Cl:
                                <strong>
                                    {{ $inr(abs($gCl)) }} {{ $gCl < 0 ? 'Cr' : 'Dr' }}
                                </strong>
                            </span> -->
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="sticky top-0 z-10 bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] backdrop-blur-md border-b border-cyan-500/20">
                                <tr class="text-black-600 dark:text-gray-300">
                                    <th class="px-4 py-2 font-bold">Ledger</th>
                                    <th class="px-4 py-2 font-bold">Parent</th>
                                    {{-- <th class="px-4 py-2 font-semibold text-center">Side</th> --}}
                                    <th class="px-4 py-2 font-bold text-right">Opening</th>
                                    <th class="px-4 py-2 font-bold text-right">Debit</th>
                                    <th class="px-4 py-2 font-bold text-right">Credit</th>
                                    <th class="px-4 py-2 font-bold text-right">Closing</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 tabular-nums">
                                @foreach ($filteredList as $r)
                                    @php
                                        $op = $toFloat($r->decOpBl ?? 0);
                                        $cl = $toFloat($r->decClBl ?? 0);
                                        $rb = $toFloat($r->decRunningBalance ?? 0);
                                        $dr = $toFloat($r->decDr ?? 0);
                                        $cr = $toFloat($r->decCr ?? 0);
                                        $side = $sideByClosing($r);
                                    @endphp
                                    <!-- hover:bg-transparent  -->
                                    <tr
                                        class="group  hover:backdrop-blur-md hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] transition-all duration-300 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80">
                                        <td class="px-4 py-2 group-hover:text-black">
                                            
                                            <a href="{{ route('reports.voucher_history', ['ledger_id' => $r->iLedgerId ?? null, 'from' => $queryParams['from'], 'to' => $queryParams['to']]) }}"
                                                class="text-blue-600 hover:underline group-hover:text-black">
                                                <div class="text-gray-900 dark:text-gray-100 group-hover:text-black">
                                                    {{ $r->strCustomerName ?? 'Ledger' }}</div>
                                                {{-- <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    #{{ $r->iLedgerId ?? '-' }}</div> --}}
                                            </a>
                                        </td>
                                        <td class="px-4 py-2 text-black-700 dark:text-gray-300 group-hover:text-black">{{ $r->strParents ?? '-' }}
                                        </td>
                                        {{-- <td class="px-4 py-2 text-center">
                                            @if ($side === 'Dr')
                                                <span
                                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">Dr</span>
                                            @else
                                                <span
                                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Cr</span>
                                            @endif
                                        </td> --}}
                                        <td
                                            class="px-4 py-2 group-hover:text-black  text-right {{ $op < 0 ? 'text-black-700 dark:text-gray-300' : 'text-black-700 dark:text-gray-300 ' }}">
                                            <!-- {{ $inr($op) }} -->
                                            @php
                                                $opSide = $op <= 0 ? 'Dr' : 'Cr';
                                            @endphp
                                            @if(abs($op) > 0)
                                                {{ $inr(abs($op)) }} {{ $op < 0 ? 'Dr' : 'Cr' }}
                                            @else
                                                0.00
                                            @endif

                                        </td>
                                        <td
                                            class="px-4 py-2 group-hover:text-black text-right {{ $dr > 0 ? 'text-black-700 dark:text-gray-300' : 'text-black-700 dark:text-gray-300 ' }}">
                                            {{ $inr($dr) }}
                                        </td>
                                        <td
                                            class="px-4 py-2 group-hover:text-black text-right {{ $cr > 0 ? 'text-black-700 dark:text-gray-300' : 'text-black-700 dark:text-gray-300 ' }}">
                                            {{ $inr($cr) }}
                                        </td>
                                        <td
                                            class="px-4 py-2 group-hover:text-black text-right {{ $cl < 0 ? 'text-black-700 dark:text-gray-300' : 'text-black-700 dark:text-gray-300 ' }}">
                                            <!-- {{ $inr($cl) }} -->
                                            @php
                                                $side = $cl <= 0 ? 'Dr' : 'Cr';
                                            @endphp
                                            
                                            @if(abs($cl) > 0)
                                                {{ $inr(abs($cl)) }} {{ $cl < 0 ? 'Dr' : 'Cr' }}
                                            @else
                                                0.00
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class=" border-t border-gray-600">
                                <tr>
                                    <td colspan="2" class="px-4 py-3">
                                        <!-- <div class="flex flex-wrap items-center justify-end gap-6 text-sm">
                                            <span class="text-gray-700 dark:text-gray-300">Group Dr:
                                                <strong
                                                    class="text-gray-700 dark:text-gray-300">{{ $inr($gDr) }}</strong></span>
                                            <span class="text-gray-700 dark:text-gray-300">Group Cr:
                                                <strong
                                                    class="text-gray-600 dark:text-gray-300">{{ $inr($gCr) }}</strong></span>
                                        </div> -->
                                        <div class="flex flex-wrap items-center justify-end gap-6 text-sm">
                                            <span class="text-gray-700 dark:text-gray-300"><strong
                                                    class="text-black-700 dark:text-gray-300">Total</strong>
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-2 group-hover:text-black  text-right {{ $op < 0 ? 'text-black-700 dark:text-gray-300' : 'text-black-700 dark:text-gray-300 ' }}">
                                        <!-- {{ $inr($op) }} -->
                                        <strong>
                                            @if(abs($gOp) > 0)
                                                {{ $inr(abs($gOp)) }} {{ $gOp < 0 ? 'Dr' : 'Cr' }}
                                            @else
                                                0.00
                                            @endif</strong>
                                    </td>
                                    <td
                                        class="px-4 py-2 group-hover:text-black text-right {{ $dr > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-700 dark:text-gray-300 ' }}">
                                        <strong>{{ $inr($gDr) }}</strong>
                                    </td>
                                    <td
                                        class="px-4 py-2 group-hover:text-black text-right {{ $cr > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-700 dark:text-gray-300 ' }}">
                                        <strong>{{ $inr($gCr) }}</strong>
                                    </td>
                                    
                                    <td
                                        class="px-4 py-2 group-hover:text-black text-right {{ $cl < 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-700 dark:text-gray-300 ' }}">
                                        <!-- {{ $inr($cl) }} -->
                                        <strong> 
                                            @if(abs($gCl) > 0)
                                                {{ $inr(abs($gCl)) }} {{ $gCl < 0 ? 'Dr' : 'Cr' }}
                                            @else
                                                0.00
                                            @endif
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                 @endif
            @empty
                <div
                    class="mt-4 rounded-md bg-yellow-50 dark:bg-yellow-900/20 px-4 py-3 text-sm text-yellow-800 dark:text-yellow-300">
                    No ledgers found for the selected period.
                </div>
            @endforelse
        </div>

        {{-- Net bar --}}
        <div
            class="mt-2 rounded-md border border-gray-200 dark:border-gray-700  p-4 flex flex-wrap items-center justify-between">
            <!-- <div class="text-sm text-gray-600 dark:text-gray-300">
                <strong>Total Dr:</strong> <span
                    class="text-gray-700 dark:text-gray-300">{{ $inr($totalDr) }}</span>
                &nbsp;|&nbsp;
                <strong>Total Cr:</strong> <span class="text-gray-700 dark:text-gray-300">{{ $inr($totalCr) }}</span>
            </div> -->
            <div class="flex flex-wrap justify-end gap-6 text-sm text-right w-full">
                <span>Opening: <strong>
                    @if(abs($totalOp) > 0)
                        {{ $inr(abs($totalOp)) }} {{ $totalOp < 0 ? 'Dr' : 'Cr' }}
                    @else
                        0.00
                    @endif
                </strong></span> |
                <span>Debit: <strong>{{ $inr($totalDr) }}</strong></span> |
                <span>Credit: <strong>{{ $inr($totalCr) }}</strong></span> |
                <span>Closing: <strong>
                    @if(abs($totalCl) > 0)
                        {{ $inr(abs($totalCl)) }} {{ $totalCl < 0 ? 'Dr' : 'Cr' }}
                    @else
                        0.00
                    @endif
                </strong></span>
            </div>
            <!-- <div class="text-sm">
                @if (abs($net) < 0.005)
                    <span class="text-emerald-700 dark:text-emerald-300 font-semibold">Balanced (Dr = Cr)</span>
                @elseif ($net > 0)
                    <span class="text-gray-900 dark:text-gray-100">Net: <strong
                            class="text-gray-700 dark:text-gray-300">{{ $inr($net) }} Dr</strong></span>
                @else
                    <span class="text-gray-900 dark:text-gray-100">Net: <strong
                            class="text-gray-700 dark:text-gray-300">{{ $inr(abs($net)) }} Cr</strong></span>
                @endif
            </div> -->
        </div>
    </div>

    <script>
        window.financialYearOptions = @json(collect($financialYears ?? [])->mapWithKeys(fn ($year) => [(string) $year->iYearId => $year->strYear])->all());

        function getRangeValue() {
            return document.querySelector('input[name="range"]').value;
        }

        function applyPresetRange(kind) {
            const pad = n => String(n).padStart(2, '0');
            const fmt = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

            function firstDayOfMonth(y, m) {
                return new Date(y, m, 1);
            }

            function lastDayOfMonth(y, m) {
                return new Date(y, m + 1, 0);
            }

            function fyStartYearFor(date) {
                return date.getMonth() >= 3 ? date.getFullYear() : date.getFullYear() - 1;
            }

            function fyRange(startYr) {
                const from = new Date(startYr, 3, 1);
                const to = new Date(startYr + 1, 2, 31);
                return { from, to };
            }

            function fyQuarterIndex(date) {
                const shifted = (date.getMonth() + 12 - 3) % 12;
                return Math.floor(shifted / 3) + 1;
            }

            function fyQuarterRange(fyStartYr, q) {
                let from, to;
                if (q === 4) {
                    from = new Date(fyStartYr + 1, 0, 1);
                    to = lastDayOfMonth(fyStartYr + 1, 2);
                } else {
                    const startM = 3 + (q - 1) * 3;
                    from = new Date(fyStartYr, startM, 1);
                    to = lastDayOfMonth(fyStartYr, startM + 2);
                }
                return { from, to };
            }

            function financialYearRange(kind) {
                const match = /^(\d{4})-(\d{4})$/.exec(window.financialYearOptions?.[kind] || '');

                if (!match) return null;

                return {
                    from: new Date(Number(match[1]), 3, 1),
                    to: new Date(Number(match[2]), 2, 31)
                };
            }

            function computeRange(kind) {
                const selectedFinancialYear = financialYearRange(kind);
                if (selectedFinancialYear) return selectedFinancialYear;

                const now = new Date();
                const y = now.getFullYear();
                const m = now.getMonth();

                if (kind === 'this_month') return {
                    from: firstDayOfMonth(y, m),
                    to: lastDayOfMonth(y, m)
                };
                if (kind === 'last_month') {
                    const prevY = m === 0 ? y - 1 : y;
                    const prevM = m === 0 ? 11 : m - 1;
                    return {
                        from: firstDayOfMonth(prevY, prevM),
                        to: lastDayOfMonth(prevY, prevM)
                    };
                }

                const fyStartYr = fyStartYearFor(now);
                if (kind === 'current_year') return fyRange(fyStartYr);
                if (kind === 'last_year') return fyRange(fyStartYr - 1);

                if (kind === 'current_quarter') {
                    const q = fyQuarterIndex(now);
                    return fyQuarterRange(fyStartYr, q);
                }
                if (kind === 'last_quarter') {
                    let q = fyQuarterIndex(now) - 1;
                    let startYr = fyStartYr;
                    if (q === 0) {
                        q = 4;
                        startYr = fyStartYr - 1;
                    }
                    return fyQuarterRange(startYr, q);
                }

                return null;
            }

            const r = computeRange(kind);
            if (r) {
                document.getElementById('from').value = fmt(r.from);
                document.getElementById('to').value = fmt(r.to);
            }
        }

        function autoSubmitIfPresetRange() {
            const form = document.getElementById('searchForm');
            const range = document.querySelector('input[name="range"]').value;
            // ONLY AUTO SEARCH FOR NON CUSTOM
            if (range !== 'custom') {
                applyPresetRange(range);
                form.submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('searchForm');
            //const rangeSel = document.getElementById('rangeSel');
            
            const customSearchButtons = document.getElementById('customSearchButtons');
            const autoSearchElements = document.querySelectorAll('.auto-search');
            
            let autoSearchTimeout;
            //let isCustomRange = rangeSel.value === 'custom';
            let isCustomRange = getRangeValue() === 'custom';

            // Toggle custom date fields visibility
            function toggleCustomFields(show) {
                document.getElementById('customFromWrap').classList.toggle('hidden', !show);
                document.getElementById('customToLabel').classList.toggle('hidden', !show);
                document.getElementById('customToWrap').classList.toggle('hidden', !show);
                customSearchButtons.classList.toggle('hidden', !show);
                isCustomRange = show;
            }

            // Initialize
            toggleCustomFields(isCustomRange);

            // Range selector change handler
            // rangeSel.addEventListener('change', function() {
            //     const isCustom = this.value === 'custom';
            //     toggleCustomFields(isCustom);
                
            //     // Auto-submit for non-custom ranges
            //     if (!isCustom) {
            //         setTimeout(submitForm, 100);
            //     }
            // });

            // Auto-search functionality
            autoSearchElements.forEach(element => {
                element.addEventListener('change', function() {
                    if (!isCustomRange) {
                        debouncedSubmit();
                    }
                });
                
                // For input fields (ledger name), also trigger on input with debouncing
                if (element.type === 'text') {
                    element.addEventListener('input', function() {
                        if (!isCustomRange) {
                            debouncedSubmit();
                        }
                    });
                }
            });

            // Debounced form submission
            function debouncedSubmit() {
                clearTimeout(autoSearchTimeout);
                autoSearchTimeout = setTimeout(submitForm, 500); // 500ms delay
            }

            // Form submission function
            // function submitForm() {
            //     // Update hidden date fields before submission
            //     //if (rangeSel.value === 'custom') {
            //     if (getRangeValue() === 'custom') {
            //         document.getElementById('from').value = document.getElementById('from_custom').value;
            //         document.getElementById('to').value = document.getElementById('to_custom').value;
            //     } else {
            //         // For preset ranges, compute dates (using your existing logic)
            //         applyPresetRange(rangeSel.value);
            //     }
                
            //     // Show loading indicator (optional)
            //     showLoading();
                
            //     // Submit the form
            //     form.submit();
            // }

            // Your existing date range computation logic
            

            // Optional: Loading indicator
            function showLoading() {
                // You can add a loading spinner here if desired
                console.log('Searching...');
            }
        });

        document.getElementById('searchForm').addEventListener('submit', function(e) {

            const range = document.querySelector('input[name="range"]').value;

            if (range === 'custom') {

                const fromCustom = document.getElementById('from_custom').value;
                const toCustom   = document.getElementById('to_custom').value;

                // validation
                if (!toCustom) {
                    e.preventDefault();
                    showToast('Please select To Date','error');
                    return;
                }

                // 🔥 SET VALUES HERE (THIS IS YOUR MAIN FIX)
                document.getElementById('from').value = fromCustom;
                document.getElementById('to').value   = toCustom;

            } else {
                // preset range
                applyPresetRange(range);
            }
        });

        function handleRangeChange(value) {

            const fromWrap = document.getElementById('customFromWrap');
            const toWrap   = document.getElementById('customToWrap');
            const toLabel  = document.getElementById('customToLabel');
            const btnWrap  = document.getElementById('customSearchButtons');

            const isCustom = value === 'custom';

            // 🔥 safe toggle
            fromWrap && fromWrap.classList.toggle('hidden', !isCustom);
            toWrap && toWrap.classList.toggle('hidden', !isCustom);
            toLabel && toLabel.classList.toggle('hidden', !isCustom);
            btnWrap && btnWrap.classList.toggle('hidden', !isCustom);

            // auto submit for preset
            if (!isCustom) {
                document.getElementById('searchForm').submit();
            }
        }
    </script>
@endsection