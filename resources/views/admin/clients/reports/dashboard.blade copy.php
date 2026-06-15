@extends('layouts.super_admin')
@section('title', 'P & L Report')

@section('content')
    @php
        use Carbon\Carbon;
        // FY helper defaults (current FY) if no range present
        $tz = 'Asia/Kolkata';
        $today = Carbon::today($tz);
        if ($today->month < 4) {
            $currStart = Carbon::create($today->year - 1, 4, 1, 0, 0, 0, $tz);
            $currEnd = Carbon::create($today->year, 3, 31, 0, 0, 0, $tz);
        } else {
            $currStart = Carbon::create($today->year, 4, 1, 0, 0, 0, $tz);
            $currEnd = Carbon::create($today->year + 1, 3, 31, 0, 0, 0, $tz);
        }
        $lastStart = $currStart->copy()->subYear();
        $lastEnd = $currEnd->copy()->subYear();

        $fromVal = old('from', request('from', $range['from'] ?? $currStart->format('Y-m-d')));
        $toVal = old('to', request('to', $range['to'] ?? $currEnd->format('Y-m-d')));
    @endphp
    <div class="mt-6 flex items-center gap-4 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('clients.dashboard', $guid ?? '') }}"
            class="px-4 py-2 -mb-px text-sm font-medium border-b-2 transition
                {{ request()->routeIs('clients.dashboard') ||
                request()->routeIs('clients.reports.pnl') ||
                request()->routeIs('clients.reports.balanceSheet') ||
                request()->routeIs('clients.reports.ledger') ||
                request()->routeIs('clients.reports.voucherHistory')
                    ? 'border-blue-600 text-blue-600 dark:text-blue-400'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
            Financial Dashboard
        </a>

        <a href="{{ route('clients.documents.dashboard', $guid ?? '') }}"
            class="px-4 py-2 -mb-px text-sm font-medium border-b-2 transition
                {{ request()->routeIs('clients.documents.dashboard')
                    ? 'border-blue-600 text-blue-600 dark:text-blue-400'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
            Document Dashboard
        </a>
    </div>
    <div class="dashboard-main-body">
        @include('admin.clients.reports.tabmanu')
        <div class="container py-3">

            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Financial Dashboard</h1>
                {{-- <div class="text-sm text-gray-500 dark:text-gray-400">{{ $labelFY ?? '' }}</div> --}}
                <div class="flex items-center gap-3">
                    {{-- Go Back button --}}
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $labelFY ?? '' }}</div>
                    <a href="{{ url()->previous() }}"
                        class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium
                               bg-gray-200 text-gray-700 hover:bg-gray-300
                               dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Go Back
                    </a>
                </div>
            </div>

            {{-- FY picker ONLY --}}
            <form id="graphForm" method="GET" action="{{ route('clients.dashboard', $guid ?? '') }}"
                class="mt-4 bg-gray-100 dark:bg-gray-800/60 rounded-lg p-3">

                {{-- One row: FY picker (left) + tabs (right) --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4">
                    {{-- FY picker inline (label + select) --}}
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-600 dark:text-gray-300">Financial Year</span>
                        @php
                            $isLastFY =
                                $fromVal === $lastStart->format('Y-m-d') && $toVal === $lastEnd->format('Y-m-d');
                            $isCurrentFY =
                                $fromVal === $currStart->format('Y-m-d') && $toVal === $currEnd->format('Y-m-d');
                        @endphp
                        <select id="fyKey" name="fySel"
                            class="h-9 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="last" data-from="{{ $lastStart->format('Y-m-d') }}"
                                data-to="{{ $lastEnd->format('Y-m-d') }}" {{ $isLastFY ? 'selected' : '' }}>
                                FY {{ $lastStart->format('Y') }}-{{ substr($lastEnd->format('Y'), -2) }}
                            </option>
                            <option value="current" data-from="{{ $currStart->format('Y-m-d') }}"
                                data-to="{{ $currEnd->format('Y-m-d') }}"
                                {{ $isCurrentFY || (!$isLastFY && !$isCurrentFY) ? 'selected' : '' }}>
                                FY {{ $currStart->format('Y') }}-{{ substr($currEnd->format('Y'), -2) }}
                            </option>
                        </select>

                        {{-- Hidden fields actually submitted --}}
                        <input type="hidden" id="fy_from" name="from" value="{{ $fromVal }}">
                        <input type="hidden" id="fy_to" name="to" value="{{ $toVal }}">
                        <input type="hidden" id="fy_type" name="type" value="{{ (int) ($activeType ?? 1) }}">
                    </div>

                    {{-- Tabs aligned to the right, same row --}}

                </div>
            </form>
            {{-- TABS + ONE CHART --}}

            {{-- 8 summary tiles (FY-wide) --}}
            {{-- 8 financial summary cards — FA6 icons + compact sizing --}}

            @if (!empty($allTotals))
                @php
                    $fmt = fn($v) => number_format((float) $v, 2, '.', ',');

                    $cards = [
                        [
                            'key' => 'totalSale',
                            'label' => 'Sales',
                            'accent' => 'blue',
                            'icon' => 'fa-solid fa-chart-line',
                        ],
                        [
                            'key' => 'totalPurchase',
                            'label' => 'Purchase',
                            'accent' => 'amber',
                            'icon' => 'fa-solid fa-cart-shopping',
                        ],
                        [
                            'key' => 'totalCredit',
                            'label' => 'Creditors',
                            'accent' => 'violet',
                            'icon' => 'fa-solid fa-people-group',
                        ],
                        [
                            'key' => 'totalDebit',
                            'label' => 'Debtors',
                            'accent' => 'fuchsia',
                            'icon' => 'fa-solid fa-user-group',
                        ],
                        [
                            'key' => 'totalReceipt',
                            'label' => 'Receipt',
                            'accent' => 'emerald',
                            'icon' => 'fa-solid fa-arrow-down-long',
                        ],
                        [
                            'key' => 'totalPayment',
                            'label' => 'Payment',
                            'accent' => 'rose',
                            'icon' => 'fa-solid fa-arrow-up-long',
                        ],
                        ['key' => 'totalCash', 'label' => 'Cash', 'accent' => 'teal', 'icon' => 'fa-solid fa-wallet'],
                        [
                            'key' => 'totalBank',
                            'label' => 'Bank',
                            'accent' => 'indigo',
                            'icon' => 'fa-solid fa-building-columns',
                        ],
                    ];

                    $leftBar = fn($c) => match ($c) {
                        'blue' => 'bg-blue-500',
                        'amber' => 'bg-amber-500',
                        'violet' => 'bg-violet-500',
                        'fuchsia' => 'bg-fuchsia-500',
                        'emerald' => 'bg-emerald-500',
                        'rose' => 'bg-rose-500',
                        'teal' => 'bg-teal-500',
                        'indigo' => 'bg-indigo-500',
                        default => 'bg-gray-500',
                    };
                    $chip = fn($c) => match ($c) {
                        'blue' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300',
                        'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300',
                        'violet' => 'bg-violet-50 text-violet-600 dark:bg-violet-900/30 dark:text-violet-300',
                        'fuchsia' => 'bg-fuchsia-50 text-fuchsia-600 dark:bg-fuchsia-900/30 dark:text-fuchsia-300',
                        'emerald' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300',
                        'rose' => 'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-300',
                        'teal' => 'bg-teal-50 text-teal-600 dark:bg-teal-900/30 dark:text-teal-300',
                        'indigo' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300',
                        default => 'bg-gray-50 text-gray-600 dark:bg-gray-900/30 dark:text-gray-300',
                    };
                @endphp

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($cards as $c)
                        @php $val = $allTotals[$c['key']]['value'] ?? 0; @endphp
                        <form id="cardNavForm" method="GET" action="{{ route('reports.ledger') }}">
                            <button type="submit"
                                class="group block w-full text-left focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-xl">
                                <div
                                    class="relative bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[92px]">
                                    <div class="absolute inset-y-0 left-0 w-1.5 {{ $leftBar($c['accent']) }}"></div>

                                    <div class="p-4 pl-6">
                                        <div class="flex items-start justify-between">
                                            <div class="pr-3">
                                                <div
                                                    class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                                    {{ $c['label'] }}
                                                </div>
                                                {{-- smaller number: was text-2xl md:text-3xl --}}
                                                <div class="mt-0.5 text-xl md:text-2xl font-semibold leading-tight text-gray-900 dark:text-white tabular-nums"
                                                    style="font-size: 1rem !important;">
                                                    {{ $fmt($val) }}
                                                </div>
                                            </div>

                                            <div class="shrink-0">
                                                <div
                                                    class="h-9 w-9 md:h-10 md:w-10 rounded-full flex items-center justify-center {{ $chip($c['accent']) }}">
                                                    <i class="{{ $c['icon'] }} text-sm md:text-base"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="group_id" value="{{ $allTotals[$c['key']]['iGroupId'] }}" />
                                <input type="hidden" name="guid" value="{{ $guid }}" />
                                <input type="hidden" name="from" id="nav_from" value="{{ $fromVal }}">
                                <input type="hidden" name="to" id="nav_to" value="{{ $toVal }}">
                        </form>
                    @endforeach
                </div>
            @endif

            @php
                $tabLabels = [
                    1 => 'Sales vs Purchase',
                    2 => 'Creditors vs Debtors',
                    3 => 'Receipt vs Payment',
                    4 => 'Cash & Bank Flow',
                ];
                $active = (int) ($activeType ?? 1);
            @endphp
            {{-- sm:mt-0 --}}
            <div class="mt-4 sm:ml-auto flex flex-wrap items-center gap-2" role="tablist">
                @foreach ($tabLabels as $t => $label)
                    <button type="button" data-type="{{ $t }}"
                        class="h-9 px-3 text-sm rounded-md border transition
          {{ $active === $t
              ? 'bg-blue-600 text-white border-blue-600'
              : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div class="mt-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                <div class="flex items-center justify-between mb-2">
                    <h2 id="chartTitle" class="text-base font-semibold text-gray-900 dark:text-gray-100"></h2>
                    <div class="text-xs text-gray-700 dark:text-gray-300">
                        In: <strong id="totIn" class="text-emerald-700 dark:text-emerald-300">0.00</strong>
                        &nbsp;|&nbsp;
                        Out: <strong id="totOut" class="text-red-600 dark:text-red-400">0.00</strong>
                    </div>
                </div>
                <div class="h-80 w-full">
                    <canvas id="mainChart" class="h-full w-full"></canvas>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        // Server data: all four series for the selected FY
        const charts = @json($charts ?? []);
        const activeType = {{ (int) ($activeType ?? 1) }};

        const fmt = v => new Intl.NumberFormat('en-IN', {
            maximumFractionDigits: 2
        }).format(Number(v) || 0);

        // map by key for quick lookup
        const byKey = {};
        charts.forEach(c => byKey[c.key] = c);

        let chart;

        function dataset(label, arr, color) {
            return {
                label,
                data: (arr || []).map(v => Number(v) || 0),
                borderColor: color,
                backgroundColor: color,
                borderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                tension: 0.25,
                fill: false,
            };
        }

        function renderChartFor(type) {
            const c = byKey[type] || charts[0];
            if (!c) return;

            // update title & totals
            document.getElementById('chartTitle').textContent = c.title || '';
            document.getElementById('totIn').textContent = fmt(c.sumIn ?? 0);
            document.getElementById('totOut').textContent = fmt(c.sumOut ?? 0);

            // (re)draw
            const ctx = document.getElementById('mainChart').getContext('2d');
            if (chart) chart.destroy();

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: (c.months || []).map(String),
                    datasets: [
                        dataset('In', c.in, '#059669'), // emerald-600
                        dataset('Out', c.out, '#dc2626'), // red-600
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // obey CSS height
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                autoSkip: true,
                                maxRotation: 0
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => fmt(v)
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.dataset.label}: ${fmt(ctx.parsed.y)}`
                            }
                        }
                    }
                }
            });

            // style active tab
            document.querySelectorAll('[data-type]').forEach(btn => {
                const isActive = Number(btn.dataset.type) === Number(type);
                btn.classList.toggle('bg-blue-600', isActive);
                btn.classList.toggle('text-white', isActive);
                btn.classList.toggle('border-blue-600', isActive);

                btn.classList.toggle('bg-white', !isActive);
                btn.classList.toggle('dark:bg-gray-900', !isActive);
                btn.classList.toggle('text-gray-700', !isActive);
                btn.classList.toggle('dark:text-gray-300', !isActive);
            });

            // keep selection for FY change
            const typeHidden = document.getElementById('type');
            if (typeHidden) typeHidden.value = type;
        }

        // Initial draw
        renderChartFor(activeType);

        // Tab switching (no reload)
        document.querySelectorAll('[data-type]').forEach(btn => {
            btn.addEventListener('click', () => renderChartFor(Number(btn.dataset.type)));
        });
    </script>
    <script>
        // Scope everything to the form to avoid collisions
        const form = document.getElementById('graphForm');
        const fySel = document.getElementById('fyKey');
        const hidFrom = form.querySelector('#fy_from');
        const hidTo = form.querySelector('#fy_to');
        const hidType = form.querySelector('#fy_type');

        // When user switches tabs, keep the selection in the hidden field
        function setActiveType(t) {
            if (hidType) hidType.value = String(t);
        }

        // FY quick-fill: update hidden dates then submit
        fySel.addEventListener('change', () => {
            const opt = fySel.options[fySel.selectedIndex];
            const f = opt.getAttribute('data-from');
            const t = opt.getAttribute('data-to');
            if (f && t) {
                hidFrom.value = f;
                hidTo.value = t;
            }
            if (form.requestSubmit) form.requestSubmit();
            else form.submit();
        });

        // In your tab click handler (where you previously set #type), call:
        // setActiveType(Number(btn.dataset.type));
    </script>
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
