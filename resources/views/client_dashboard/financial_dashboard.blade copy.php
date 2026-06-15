@php
    use Carbon\Carbon;
    // FY helper defaults (current FY) if range not present
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

    // INR formatter
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
@endphp

<div class="container py-3">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Financial Dashboard</h1>
        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $labelFY ?? '' }}</div>
    </div>

    {{-- ONLY FY picker (no type dropdown) --}}
    <form method="GET" action="{{ route('home') }}"
        class="mt-4 bg-gray-100 dark:bg-gray-800/60 rounded-lg p-4 flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">Financial Year</label>
            <select id="fyKey"
                class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                <option value="last" data-from="{{ $lastStart->format('Y-m-d') }}"
                    data-to="{{ $lastEnd->format('Y-m-d') }}">
                    FY {{ $lastStart->format('Y') }}-{{ substr($lastEnd->format('Y'), -2) }}
                </option>
                <option value="current" data-from="{{ $currStart->format('Y-m-d') }}"
                    data-to="{{ $currEnd->format('Y-m-d') }}" selected>
                    FY {{ $currStart->format('Y') }}-{{ substr($currEnd->format('Y'), -2) }}
                </option>
            </select>

            <input type="hidden" id="from" name="from" value="{{ $fromVal }}">
            <input type="hidden" id="to" name="to" value="{{ $toVal }}">
        </div>

        <div class="ml-auto flex gap-2">
            <button class="rounded-md bg-blue-600 text-white px-4 py-2 text-sm hover:bg-blue-700">Apply</button>
            <a href="{{ route('home') }}"
                class="rounded-md bg-red-600 text-white px-4 py-2 text-sm hover:bg-red-700">Reset</a>
        </div>
    </form>

    {{-- 8 summary tiles --}}
    @if (!empty($allTotals))
        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
            @php
                $labels = [
                    'totalSale' => 'Sales',
                    'totalPurchase' => 'Purchase',
                    'totalCredit' => 'Creditors',
                    'totalDebit' => 'Debtors',
                    'totalReceipt' => 'Receipt',
                    'totalPayment' => 'Payment',
                    'totalCash' => 'Cash',
                    'totalBank' => 'Bank',
                ];
            @endphp
            @foreach ($labels as $k => $label)
                @php $v = (float) ($allTotals[$k]['value'] ?? 0); @endphp
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3 bg-white dark:bg-gray-900">
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ number_format($v, 2, '.', ',') }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- FOUR CHARTS --}}
    <div class="mt-6  space-y-6">
        @foreach ($charts ?? [] as $c)
            @php
                $sumIn = array_sum(array_map('floatval', $c['in'] ?? []));
                $sumOut = array_sum(array_map('floatval', $c['out'] ?? []));
            @endphp

            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-base font-semibold">{{ $c['title'] }}</h2>
                    <div class="text-xs">
                        In: <strong class="text-emerald-700 dark:text-emerald-300">{{ $inr($sumIn) }}</strong>
                        &nbsp;|&nbsp;
                        Out: <strong class="text-red-600 dark:text-red-400">{{ $inr($sumOut) }}</strong>
                    </div>
                </div>

                <div class="h-80 w-full">
                    <canvas id="chart-{{ $c['key'] }}" class="h-full w-full"></canvas>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    // FY quick-fill -> set hidden from/to
    document.getElementById('fyKey').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const f = opt.getAttribute('data-from');
        const t = opt.getAttribute('data-to');
        if (f && t) {
            document.getElementById('from').value = f;
            document.getElementById('to').value = t;
        }
    });

    const charts = @json($charts ?? []);

    // helper to ensure numeric data & visible colors
    const mkDataset = (label, arr, color) => ({
        label,
        data: (arr || []).map(v => Number(v) || 0), // coerce to numbers
        borderColor: color,
        backgroundColor: color,
        borderWidth: 2,
        pointRadius: 2,
        pointHoverRadius: 4,
        tension: 0.25,
        fill: false,
    });

    const fmt = v => new Intl.NumberFormat('en-IN', {
        maximumFractionDigits: 2
    }).format(v);

    charts.forEach(c => {
        const el = document.getElementById(`chart-${c.key}`);
        if (!el) return;

        const labels = (c.months || []).map(String);
        const dsIn = mkDataset('In', c.in, '#059669'); // emerald-600
        const dsOut = mkDataset('Out', c.out, '#dc2626'); // red-600

        new Chart(el.getContext('2d'), {
            type: 'line',
            data: {
                labels,
                datasets: [dsIn, dsOut]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // respect CSS height
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            autoSkip: true,
                            maxRotation: 0,
                            callback: (v, i) => labels[i] ?? ''
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
    });
</script>
