@extends('layouts.super_admin')
@section('title', 'Balance Sheet')

@section('content')
    @php
        // ----- normalize -----
        $payload = $data ?? ($resp['data'] ?? []);
        $rows = collect($payload['rows'] ?? []);
        $totals = $payload['totals'] ?? ['assets' => '0', 'liabilities' => '0', 'equity' => '0'];

        $drRows = $rows->where('Side', 'DR'); // Assets
        $crRows = $rows->where('Side', 'CR'); // Liabilities & Equity

        // ----- helpers -----
        $isNeg = fn($v) => (float) $v < 0;

        // Indian number formatting (##,##,###.00)
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

        // quick diff to show balance status
        $assets = (float) ($totals['assets'] ?? 0);
        $liabs = (float) ($totals['liabilities'] ?? 0);
        $equity = (float) ($totals['equity'] ?? 0);
        $balanceDiff = round($assets - ($liabs + $equity), 2);
    @endphp


    <div class="container py-3">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Balance Sheet</h1>
            {{-- <a href="{{ url()->previous() }}"
                class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium
                      bg-gray-200 text-gray-700 hover:bg-gray-300
                      dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Go Back
            </a> --}}
        </div>
        <form method="POST" action="{{ route('reports.balance_sheet') }}"
            class="mt-5 bg-gray-100 dark:bg-gray-800/60 rounded-lg p-4 flex flex-wrap items-end gap-3">
            @csrf
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
            {{-- <div>
                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">From Date</label>
                <input type="date" name="from" id="from" value="{{ request('from') }}"
                    class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            </div>
            <div class="pb-2 text-gray-500 dark:text-gray-400">TO</div>
            <div>
                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">To Date</label>
                <input type="date" name="to" id="to" value="{{ request('to') }}"
                    class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
            </div>
            <div class="ml-auto flex gap-2">
                <button type="submit"
                    class="rounded-md bg-blue-600 text-white px-4 py-2 text-sm hover:bg-blue-700">Search</button>
                <a href="{{ route('reports.pl') }}"
                    class="rounded-md bg-red-600 text-white px-4 py-2 text-sm hover:bg-red-700">Reset</a>
            </div> --}}
        </form>

        @if ($resp['success'] ?? false)
            <div class="mt-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold">Balance Sheet</h2>
                    <p class="text-sm text-gray-500">Period: {{ $from }} to {{ $to }}</p>
                </div>
                <button onclick="window.print()" class="px-3 py-1.5 rounded bg-gray-800 text-white text-sm">Print</button>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-5">
                {{-- ASSETS (DR) --}}
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 py-3 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Assets (Dr)</h2>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($drRows as $r)
                            @php $amt = $r->decMainAmount ?? 0; @endphp
                            <a href="{{ route('reports.ledger', ['group_id' => $r->iPrimaryGroupId ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                class="text-blue-600 hover:underline">
                                <div class="flex items-center justify-between px-4 py-2 bg-gray-50 dark:bg-gray-900/40">
                                    <span
                                        class="text-sm text-gray-800 dark:text-gray-200">{{ $r->strGroupName ?? '-' }}</span>
                                    <span
                                        class="text-sm font-medium {{ $isNeg($amt) ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">
                                        {{ $inr($amt) }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">No asset rows.</div>
                        @endforelse
                    </div>
                    <div
                        class="px-4 py-3 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex justify-between">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Total (Dr)</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $inr($assets) }}</span>
                    </div>
                </div>

                {{-- LIABILITIES & EQUITY (CR) --}}
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 py-3 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Liabilities &amp; Equity (Cr)
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($crRows as $r)
                            @php $amt = $r->decMainAmount ?? 0; @endphp
                            <a href="{{ route('reports.ledger', ['group_id' => $r->iPrimaryGroupId ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                class="text-blue-600 hover:underline">
                                <div class="flex items-center justify-between px-4 py-2 bg-gray-50 dark:bg-gray-900/40">
                                    <span
                                        class="text-sm text-gray-800 dark:text-gray-200">{{ $r->strGroupName ?? '-' }}</span>
                                    <span
                                        class="text-sm font-medium {{ $isNeg($amt) ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">
                                        {{ $inr($amt) }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">No liability/equity rows.</div>
                        @endforelse
                    </div>
                    <div class="px-4 py-3 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Liabilities</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $inr($liabs) }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Equity</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $inr($equity) }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between mt-2 border-t border-gray-200 dark:border-gray-700 pt-2">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Total (Cr)</span>
                            <span
                                class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $inr($liabs + $equity) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Balance note --}}
            <div class="mt-4">
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
            </div>
        @else
            <div class="mt-4 rounded-md bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-800 dark:text-red-300">
                {{ $resp['message'] ?? 'Failed to load' }}
            </div>
        @endif
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
