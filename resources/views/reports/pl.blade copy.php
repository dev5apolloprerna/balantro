@extends('layouts.super_admin')
@section('title', 'P & L Report')

@section('content')
    <div class="container py-3">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">P & L Report</h1>
            {{-- <a href="{{ url()->previous() }}"
                class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium
                      bg-gray-200 text-gray-700 hover:bg-gray-300
                      dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Go Back
            </a> --}}
        </div>
        <form method="POST" action="{{ route('reports.pl') }}"
            class="mt-5 bg-gray-100 dark:bg-gray-800/60 rounded-lg p-4 flex flex-wrap items-end gap-3">
            @csrf
            {{-- Range preset --}}
            @php
                // pick current if provided; fall back to 'current_year'
                $rangeSel = request('range');
                if (!$rangeSel) {
                    $rangeSel = request('from') || request('to') ? 'custom' : 'current_year';
                }
            @endphp
            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Date Range</label>
                <select id="rangeSel" name="range"
                    class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <option value="current_year" {{ $rangeSel === 'current_year' ? 'selected' : '' }}>Current Year</option>
                    <option value="this_month" {{ $rangeSel === 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $rangeSel === 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="current_quarter"{{ $rangeSel === 'current_quarter' ? 'selected' : '' }}>Current Quarter
                    </option>
                    <option value="last_quarter" {{ $rangeSel === 'last_quarter' ? 'selected' : '' }}>Last Quarter</option>
                    <option value="last_year" {{ $rangeSel === 'last_year' ? 'selected' : '' }}>Last Year</option>
                    <option value="custom" {{ $rangeSel === 'custom' ? 'selected' : '' }}>Custom Date</option>
                </select>
            </div>

            {{-- Custom date inputs (shown only when range=custom) --}}
            <div id="customFromWrap" class="{{ $rangeSel === 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">From Date</label>
                <input type="date" name="from_custom" id="from_custom" value="{{ request('from') }}"
                    class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            </div>
            <div id="customToLabel"
                class="pb-2 text-gray-500 dark:text-gray-400 {{ $rangeSel === 'custom' ? '' : 'hidden' }}">TO</div>
            <div id="customToWrap" class="{{ $rangeSel === 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">To Date</label>
                <input type="date" name="to_custom" id="to_custom" value="{{ request('to') }}"
                    class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            </div>

            {{-- Hidden fields actually submitted for presets (and also for custom via JS before submit) --}}
            <input type="hidden" name="from" id="from" value="{{ request('from') }}">
            <input type="hidden" name="to" id="to" value="{{ request('to') }}">

            <div class="ml-auto flex gap-2">
                <button type="submit"
                    class="rounded-md bg-blue-600 text-white px-4 py-2 text-sm hover:bg-blue-700">Search</button>
                <a href="{{ route('reports.pl') }}"
                    class="rounded-md bg-red-600 text-white px-4 py-2 text-sm hover:bg-red-700">Reset</a>
            </div>
            {{-- <div class="ml-auto flex gap-2">
                <button type="submit"
                    class="rounded-md bg-blue-600 text-white px-4 py-2 text-sm hover:bg-blue-700">Search</button>
                <a href="{{ route('reports.pl') }}"
                    class="rounded-md bg-red-600 text-white px-4 py-2 text-sm hover:bg-red-700">Reset</a>
            </div> --}}
        </form>
        <!-- Search / Filter Form -->
        {{-- <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="post" class="row gy-2 gx-3 align-items-end">
                    <div class="col-md-3">
                        <label for="from" class="form-label mb-1">From Date</label>
                        <input type="date" id="from" name="from" class="form-control form-control-sm"
                            value="{{ request('from') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="to" class="form-label mb-1">To Date</label>
                        <input type="date" id="to" name="to" class="form-control form-control-sm"
                            value="{{ request('to') }}">
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filter
                        </button>
                    </div>

                    <div class="col-md-3">
                        <a href="{{ route('reports.pl') }}" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div> --}}

        @isset($data['error'])
            <div class="alert alert-danger">{{ $data['error'] }}</div>
        @endisset

        {{-- Example rendering: expect $data['sections'] with income/expense totals etc. --}}
        {{-- P & L Report (Trading + P&L) --}}
        <div class="mt-4 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold">P &amp; L Report</h2>
                    <p class="text-sm text-gray-500">Period: {{ $from }} to {{ $to }}</p>
                </div>
                <button onclick="window.print()" class="px-3 py-1.5 rounded bg-gray-800 text-white text-sm">Print</button>
            </div>
            @php
                $cr = $pl['cr'] ?? [];
                $dr = $pl['dr'] ?? [];
                $iInc = $pl['IndirectIncomes'] ?? [];
                $iExp = $pl['IndirectExpenses'] ?? [];
            @endphp
            @php
                // $pl = $pl ?? [];

                // $cr = $pl['cr'] ?? []; // Direct Incomes + Sales
                // $dr = $pl['dr'] ?? []; // Purchases + Direct Expenses
                // $iInc = $pl['IndirectIncomes'] ?? []; // Indirect incomes
                // $iExp = $pl['IndirectExpenses'] ?? []; // Indirect expenses

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

                // Trading (Gross)
                $directCr = $sum($cr, false); // credits come positive
                $directDr = $sum($dr, true); // expenses/purchases are negative -> take abs
                $gross = $directCr - $directDr; // +ve => Gross Profit, -ve => Gross Loss
                $grossIsProfit = $gross >= 0;
                $grossAbs = abs($gross);

                $tradingDebitTotal = $directDr + ($grossIsProfit ? $grossAbs : 0);
                $tradingCreditTotal = $directCr + (!$grossIsProfit ? $grossAbs : 0);
                $tradingTotal = max($tradingDebitTotal, $tradingCreditTotal);

                // P&L (Net)
                $indirectCr = $sum($iInc, false);
                $indirectDr = $sum($iExp, true);
                // Carry gross result to P&L: Gross Profit goes to credit; Gross Loss goes to debit
                $net = ($grossIsProfit ? $grossAbs : -$grossAbs) + ($indirectCr - $indirectDr);
                $netIsProfit = $net >= 0;
                $netAbs = abs($net);

                $plDebitTotal = $indirectDr + (!$grossIsProfit ? $grossAbs : 0) + ($netIsProfit ? $netAbs : 0);
                $plCreditTotal = $indirectCr + ($grossIsProfit ? $grossAbs : 0) + (!$netIsProfit ? $netAbs : 0);
                $plTotal = max($plDebitTotal, $plCreditTotal);
            @endphp

            {{-- TRADING ACCOUNT (Gross) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <h3 class="font-semibold mb-3">Trading A/c — Debit (Dr)</h3>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Particulars</th>
                                <th class="py-2 text-right">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($dr as $row)
                                <tr>
                                    <td class="py-2">
                                        <a href="{{ route('reports.ledger', ['group_id' => $row['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                            class="text-blue-600 hover:underline">
                                            To {{ $row['strGroupName'] ?? '—' }}
                                        </a>
                                    </td>
                                    <td class="py-2 text-right">{{ $fmt(abs((float) $row['decMainAmount'])) }}</td>
                                </tr>
                            @endforeach

                            @if ($grossIsProfit)
                                <tr class="font-medium">
                                    <td class="py-2">To Gross Profit c/o</td>
                                    <td class="py-2 text-right">{{ $fmt($grossAbs) }}</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="border-t font-semibold">
                                <td class="py-2">Total</td>
                                <td class="py-2 text-right">{{ $fmt($tradingTotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <h3 class="font-semibold mb-3">Trading A/c — Credit (Cr)</h3>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Particulars</th>
                                <th class="py-2 text-right">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($cr as $row)
                                <tr>
                                    <td class="py-2">
                                        <a href="{{ route('reports.ledger', ['group_id' => $row['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                            class="text-blue-600 hover:underline">
                                            By {{ $row['strGroupName'] ?? '—' }}
                                        </a>
                                    </td>
                                    <td class="py-2 text-right">{{ $fmt((float) $row['decMainAmount']) }}</td>
                                </tr>
                            @endforeach

                            @unless ($grossIsProfit)
                                <tr class="font-medium">
                                    <td class="py-2">By Gross Loss c/o</td>
                                    <td class="py-2 text-right">{{ $fmt($grossAbs) }}</td>
                                </tr>
                            @endunless
                        </tbody>
                        <tfoot>
                            <tr class="border-t font-semibold">
                                <td class="py-2">Total</td>
                                <td class="py-2 text-right">{{ $fmt($tradingTotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- PROFIT & LOSS ACCOUNT (Net) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <h3 class="font-semibold mb-3">Profit &amp; Loss A/c — Debit (Dr)</h3>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Particulars</th>
                                <th class="py-2 text-right">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($iExp as $row)
                                <tr>
                                    <td class="py-2">
                                        <a href="{{ route('reports.ledger', ['group_id' => $row['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                            class="text-blue-600 hover:underline">
                                            To {{ $row['strGroupName'] ?? '—' }}
                                        </a>
                                    </td>
                                    <td class="py-2 text-right">{{ $fmt(abs((float) $row['decMainAmount'])) }}</td>
                                </tr>
                            @endforeach

                            @unless ($grossIsProfit)
                                <tr class="font-medium">
                                    <td class="py-2">To Gross Loss b/f</td>
                                    <td class="py-2 text-right">{{ $fmt($grossAbs) }}</td>
                                </tr>
                            @endunless

                            @if ($netIsProfit)
                                <tr class="font-semibold">
                                    <td class="py-2">To Net Profit</td>
                                    <td class="py-2 text-right">{{ $fmt($netAbs) }}</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="border-t font-semibold">
                                <td class="py-2">Total</td>
                                <td class="py-2 text-right">{{ $fmt($plTotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <h3 class="font-semibold mb-3">Profit &amp; Loss A/c — Credit (Cr)</h3>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Particulars</th>
                                <th class="py-2 text-right">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($iInc as $row)
                                <tr>
                                    <td class="py-2">
                                        <a href="{{ route('reports.ledger', ['group_id' => $row['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                            class="text-blue-600 hover:underline">
                                            By {{ $row['strGroupName'] ?? '—' }}
                                        </a>
                                    </td>
                                    <td class="py-2 text-right">{{ $fmt((float) $row['decMainAmount']) }}</td>
                                </tr>
                            @endforeach

                            @if ($grossIsProfit)
                                <tr class="font-medium">
                                    <td class="py-2">By Gross Profit b/f</td>
                                    <td class="py-2 text-right">{{ $fmt($grossAbs) }}</td>
                                </tr>
                            @endif

                            @unless ($netIsProfit)
                                <tr class="font-semibold">
                                    <td class="py-2">By Net Loss</td>
                                    <td class="py-2 text-right">{{ $fmt($netAbs) }}</td>
                                </tr>
                            @endunless
                        </tbody>
                        <tfoot>
                            <tr class="border-t font-semibold">
                                <td class="py-2">Total</td>
                                <td class="py-2 text-right">{{ $fmt($plTotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Summary pills --}}
            <div class="flex flex-wrap gap-3">
                <span class="px-3 py-1.5 rounded-full bg-green-100 text-green-800 text-xs">
                    Direct Cr: {{ $fmt($directCr) }}
                </span>
                <span class="px-3 py-1.5 rounded-full bg-red-100 text-red-800 text-xs">
                    Direct Dr: {{ $fmt($directDr) }}
                </span>
                <span
                    class="px-3 py-1.5 rounded-full {{ $grossIsProfit ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs">
                    {{ $grossIsProfit ? 'Gross Profit' : 'Gross Loss' }}: {{ $fmt($grossAbs) }}
                </span>
                <span class="px-3 py-1.5 rounded-full bg-green-100 text-green-800 text-xs">
                    Indirect Cr: {{ $fmt($indirectCr) }}
                </span>
                <span class="px-3 py-1.5 rounded-full bg-red-100 text-red-800 text-xs">
                    Indirect Dr: {{ $fmt($indirectDr) }}
                </span>
                <span
                    class="px-3 py-1.5 rounded-full {{ $netIsProfit ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs">
                    {{ $netIsProfit ? 'Net Profit' : 'Net Loss' }}: {{ $fmt($netAbs) }}
                </span>
            </div>
        </div>

    </div>

    <script>
        (function() {
            const sel = document.getElementById('rangeSel');
            const hidFrom = document.getElementById('from');
            const hidTo = document.getElementById('to');

            const cfWrap = document.getElementById('customFromWrap');
            const ctLbl = document.getElementById('customToLabel');
            const ctWrap = document.getElementById('customToWrap');
            const fromC = document.getElementById('from_custom');
            const toC = document.getElementById('to_custom');

            // --- helpers ---
            const pad = n => String(n).padStart(2, '0');
            const fmt = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

            function firstDayOfMonth(y, m) {
                return new Date(y, m, 1);
            } // m: 0..11
            function lastDayOfMonth(y, m) {
                return new Date(y, m + 1, 0);
            }

            // Financial year starts on April 1st
            function fyStartYearFor(date) {
                // Apr (3) .. Dec (11) -> same year; Jan/Feb/Mar -> previous year
                return date.getMonth() >= 3 ? date.getFullYear() : date.getFullYear() - 1;
            }

            function fyRange(startYr) {
                const from = new Date(startYr, 3, 1); // Apr 1, startYr
                const to = new Date(startYr + 1, 2, 31); // Mar 31, next year
                return {
                    from,
                    to
                };
            }

            // Financial quarter: Q1=Apr-Jun, Q2=Jul-Sep, Q3=Oct-Dec, Q4=Jan-Mar
            function fyQuarterIndex(date) {
                const shifted = (date.getMonth() + 12 - 3) % 12; // Apr->0 ... Mar->11
                return Math.floor(shifted / 3) + 1; // 1..4
            }

            function fyQuarterRange(fyStartYr, q) {
                let from, to;
                if (q === 4) {
                    from = new Date(fyStartYr + 1, 0, 1); // Jan 1 (next calendar year)
                    to = lastDayOfMonth(fyStartYr + 1, 2); // Mar 31
                } else {
                    const startM = 3 + (q - 1) * 3; // 3,6,9
                    from = new Date(fyStartYr, startM, 1); // Apr/Jul/Oct 1
                    to = lastDayOfMonth(fyStartYr, startM + 2); // Jun/Sep/Dec end
                }
                return {
                    from,
                    to
                };
            }

            // Build ranges (now financial-year aware)
            function computeRange(kind) {
                const now = new Date();
                const y = now.getFullYear();
                const m = now.getMonth();

                // Month ranges still calendar months (by design)
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

                // Financial year & quarters
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

                return null; // custom
            }

            function toggleCustom(show) {
                [cfWrap, ctLbl, ctWrap].forEach(el => el.classList.toggle('hidden', !show));
            }

            function applyPreset(kind) {
                const r = computeRange(kind);
                if (r) {
                    hidFrom.value = fmt(r.from);
                    hidTo.value = fmt(r.to);
                    if (!fromC.value) fromC.value = hidFrom.value; // optional seeding
                    if (!toC.value) toC.value = hidTo.value;
                }
            }

            // change handler
            sel.addEventListener('change', () => {
                const kind = sel.value;
                const isCustom = kind === 'custom';
                toggleCustom(isCustom);
                if (!isCustom) {
                    applyPreset(kind);
                } else {
                    hidFrom.value = fromC.value || '';
                    hidTo.value = toC.value || '';
                }
            });

            // before submit: ensure hidden from/to are correct
            sel.form.addEventListener('submit', () => {
                if (sel.value === 'custom') {
                    hidFrom.value = fromC.value || '';
                    hidTo.value = toC.value || '';
                } else {
                    applyPreset(sel.value);
                }
            });

            // initial setup
            if (sel.value !== 'custom') {
                applyPreset(sel.value);
                toggleCustom(false);
            } else {
                toggleCustom(true);
            }
        })();
    </script>

@endsection
