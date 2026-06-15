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
@include('client_dashboard.topmenu')
<div class="container py-3">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Financial Dashboard</h1>
        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $labelFY ?? '' }}</div>
    </div>

    {{-- FY picker ONLY --}}
    {{-- <form id="graphForm" method="GET" action="{{ route('home') }}"
        class="mt-4 bg-gray-100 dark:bg-gray-800/60 rounded-lg p-3">

        <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4">
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-600 dark:text-gray-300">Financial Year</span>
                @php
                    $isLastFY = $fromVal === $lastStart->format('Y-m-d') && $toVal === $lastEnd->format('Y-m-d');
                    $isCurrentFY = $fromVal === $currStart->format('Y-m-d') && $toVal === $currEnd->format('Y-m-d');
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

                <input type="hidden" id="fy_from" name="from" value="{{ $fromVal }}">
                <input type="hidden" id="fy_to" name="to" value="{{ $toVal }}">
                <input type="hidden" id="fy_type" name="type" value="{{ (int) ($activeType ?? 1) }}">
            </div>
        </div>
    </form> --}}

    <form id="graphForm" method="GET" action="{{ route('home') }}"
        class="mt-4 bg-gray-100 dark:bg-gray-800/60 rounded-lg p-3">

        <!-- Hidden field to preserve active tab -->
        <input type="hidden" name="tab" value="financial">

        {{-- One row: FY picker (left) + tabs (right) --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4">
            {{-- FY picker inline (label + select) --}}
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-600 dark:text-gray-300">Financial Year</span>
                @php
                    $isLastFY = $fromVal === $lastStart->format('Y-m-d') && $toVal === $lastEnd->format('Y-m-d');
                    $isCurrentFY = $fromVal === $currStart->format('Y-m-d') && $toVal === $currEnd->format('Y-m-d');
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
        </div>
    </form>
    {{-- <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="mt-4 sm:ml-auto flex flex-wrap items-center gap-2" role="tablist">
            <a href="{{ route('reports.pl') }}" style="padding-top: 0.40rem;"
                class="h-9 px-3 text-sm rounded-md border transition
           {{ request()->routeIs('reports.pl')
               ? 'bg-blue-600 text-white border-blue-600'
               : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50' }}">
                P & L Report
            </a>

            <a href="{{ route('reports.balance_sheet') }}" style="padding-top: 0.40rem;"
                class="h-9 px-3 text-sm rounded-md border transition
           {{ request()->routeIs('reports.balanceSheet')
               ? 'bg-blue-600 text-white border-blue-600'
               : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50' }}">
                Balance Sheet
            </a>

            <a href="{{ route('reports.ledger') }}" style="padding-top: 0.40rem;"
                class="h-9 px-3 text-sm rounded-md border transition
           {{ request()->routeIs('reports.ledger')
               ? 'bg-blue-600 text-white border-blue-600'
               : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50' }}">
                Ledger Report
            </a>
        </div>
    </div> --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="mt-4 sm:ml-auto flex flex-wrap items-center gap-2" role="tablist">
            <a href="{{ route('reports.balance_sheet', ['tab' => 'financial']) }}" style="padding-top: 0.40rem;"
                class="h-9 px-3 text-sm rounded-md border transition
           {{ request()->routeIs('reports.balanceSheet')
               ? 'bg-blue-600 text-white border-blue-600'
               : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50' }}">
                Balance Sheet
            </a>

            <a href="{{ route('reports.pl', ['tab' => 'financial']) }}" style="padding-top: 0.40rem;"
                class="h-9 px-3 text-sm rounded-md border transition
           {{ request()->routeIs('reports.pl')
               ? 'bg-blue-600 text-white border-blue-600'
               : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50' }}">
                Profit & Loss A/C
            </a>
            <a href="{{ route('reports.ledger', ['tab' => 'financial']) }}" style="padding-top: 0.40rem;"
                class="h-9 px-3 text-sm rounded-md border transition
           {{ request()->routeIs('reports.ledger')
               ? 'bg-blue-600 text-white border-blue-600'
               : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50' }}">
                All Ledger
            </a>
        </div>
    </div>
    {{-- TABS + ONE CHART --}}

    {{-- 8 summary tiles (FY-wide) --}}
    {{-- 8 financial summary cards — FA6 icons + compact sizing --}}

    @if (!empty($allTotals))
        @php
            $fmt = fn($v) => number_format((float) $v, 2, '.', ',');

            $cards = [
                ['key' => 'totalSale', 'label' => 'Sales', 'accent' => 'blue', 'icon' => 'fa-solid fa-chart-line'],
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

                    </button>
                    <input type="hidden" name="group_id" value="{{ $allTotals[$c['key']]['iGroupId'] }}" />
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
// Add this to your financial_dashboard.blade.php
<script>
    // Tab switching between financial and documents
    document.addEventListener('DOMContentLoaded', function() {
        const financialTab = document.querySelector('a[href*="tab=financial"]');
        const documentsTab = document.querySelector('a[href*="tab=documents"]');

        // Show/hide content based on URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'financial';

        if (activeTab === 'documents') {
            // Hide financial dashboard, show document count
            document.querySelector('.financial-content')?.style.display = 'none';
            document.querySelector('.documents-content')?.style.display = 'block';
        } else {
            // Show financial dashboard, hide document count
            document.querySelector('.financial-content')?.style.display = 'block';
            document.querySelector('.documents-content')?.style.display = 'none';
        }
    });
</script>
<script>
    // Update the form submission to preserve tab
    const form = document.getElementById('graphForm');
    if (form) {
        const fySel = document.getElementById('fyKey');
        const hidFrom = form.querySelector('#fy_from');
        const hidTo = form.querySelector('#fy_to');
        const hidType = form.querySelector('#fy_type');

        // When user switches tabs, keep the selection in the hidden field
        function setActiveType(t) {
            if (hidType) hidType.value = String(t);

            // Also update the URL without page reload for better UX
            const url = new URL(window.location);
            url.searchParams.set('type', t);
            url.searchParams.set('tab', 'financial'); // Ensure financial tab is active
            window.history.replaceState({}, '', url);
        }

        // FY quick-fill: update hidden dates then submit
        if (fySel) {
            fySel.addEventListener('change', () => {
                const opt = fySel.options[fySel.selectedIndex];
                const f = opt.getAttribute('data-from');
                const t = opt.getAttribute('data-to');
                if (f && t) {
                    hidFrom.value = f;
                    hidTo.value = t;
                }

                // Ensure tab parameter is included
                const tabInput = form.querySelector('input[name="tab"]');
                if (!tabInput) {
                    const hiddenTab = document.createElement('input');
                    hiddenTab.type = 'hidden';
                    hiddenTab.name = 'tab';
                    hiddenTab.value = 'financial';
                    form.appendChild(hiddenTab);
                }

                if (form.requestSubmit) form.requestSubmit();
                else form.submit();
            });
        }

        // Update tab click handler to use setActiveType
        document.querySelectorAll('[data-type]').forEach(btn => {
            btn.addEventListener('click', () => {
                const type = Number(btn.dataset.type);
                setActiveType(type);
                renderChartFor(type);
            });
        });
    }
</script>
