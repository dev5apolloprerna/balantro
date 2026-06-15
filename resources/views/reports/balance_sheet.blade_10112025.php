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

        // For charts - categorize assets and liabilities
        $currentAssets = $drRows->where('GroupType', 'Current Asset')->sum('decMainAmount');
        $fixedAssets = $drRows->where('GroupType', 'Fixed Asset')->sum('decMainAmount');
        $otherAssets = $drRows->sum('decMainAmount') - $currentAssets - $fixedAssets;

        $currentLiabilities = $crRows->where('GroupType', 'Current Liability')->sum('decMainAmount');
        $longTermLiabilities = $crRows->where('GroupType', 'Long Term Liability')->sum('decMainAmount');
        $otherLiabilities = $crRows->sum('decMainAmount') - $currentLiabilities - $longTermLiabilities - $equity;
    @endphp


    <div class="container py-3">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Balance Sheet</h1>
            <div>
                <div class="export-buttons mb-3">
                    @php
                        $queryParams = array_merge(request()->query(), [
                            'from' => request('from', $from ?? ''),
                            'to' => request('to', $to ?? ''),
                            'range' => request('range', $rangeSel ?? ''),
                        ]);
                    @endphp
                    <a href="{{ route('reports.balance-sheet.export-pdf', $queryParams) }}" title="Export into PDF"
                        class="btn btn-danger bg-red-600 text-white px-4 py-2 text-sm hover:bg-red-700">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                    &nbsp;
                    <a href="{{ route('reports.balance-sheet.export-excel', $queryParams) }}" title="Export into Excel"
                        class="btn btn-success bg-green-600 text-white px-4 py-2 text-sm hover:bg-green-700">
                        <i class="fas fa-file-excel"></i>
                    </a>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('reports.balance_sheet') }}"
            class="mt-5 bg-gray-100 dark:bg-gray-800/60 rounded-lg p-4 flex flex-wrap items-end gap-3">
            @csrf
            @php
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

            <input type="hidden" name="from" id="from" value="{{ request('from') }}">
            <input type="hidden" name="to" id="to" value="{{ request('to') }}">

            <div class="ml-auto flex gap-2">
                <button type="submit"
                    class="rounded-md bg-blue-600 text-white px-4 py-2 text-sm hover:bg-blue-700">Search</button>
                <a href="{{ route('reports.balance_sheet') }}"
                    class="rounded-md bg-red-600 text-white px-4 py-2 text-sm hover:bg-red-700">Reset</a>
            </div>
        </form>

        @if ($resp['success'] ?? false)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- LEFT COLUMN: DETAILED REPORT --}}
            <div class="space-y-6">
				<div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Balance Sheet Report</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Period: {{ $from }} to {{ $to }}</p>
                    </div>
                </div>
                {{-- ASSETS (DR) --}}
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800 shadow-sm">
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Assets (Dr)</h2>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($drRows as $r)
                            @php $amt = $r->decMainAmount ?? 0; @endphp
                            <a href="{{ route('reports.ledger', ['group_id' => $r->iPrimaryGroupId ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                class="block hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center justify-between px-4 py-3">
                                    <span class="text-gray-800 dark:text-gray-200">{{ $r->strGroupName ?? '-' }}</span>
                                    <span class="font-medium {{ $isNeg($amt) ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">
                                        {{ $inr($amt) }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-3 text-gray-500 dark:text-gray-400 text-center">No asset rows.</div>
                        @endforelse
                    </div>
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex justify-between">
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Total (Dr)</span>
                        <span class="font-bold text-gray-900 dark:text-gray-100">{{ $inr($assets) }}</span>
                    </div>
                </div>

                {{-- LIABILITIES & EQUITY (CR) --}}
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800 shadow-sm">
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Liabilities &amp; Equity (Cr)</h2>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($crRows as $r)
                            @php $amt = $r->decMainAmount ?? 0; @endphp
                            <a href="{{ route('reports.ledger', ['group_id' => $r->iPrimaryGroupId ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                class="block hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center justify-between px-4 py-3">
                                    <span class="text-gray-800 dark:text-gray-200">{{ $r->strGroupName ?? '-' }}</span>
                                    <span class="font-medium {{ $isNeg($amt) ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">
                                        {{ $inr($amt) }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-3 text-gray-500 dark:text-gray-400 text-center">No liability/equity rows.</div>
                        @endforelse
                    </div>
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Liabilities</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">{{ $inr($liabs) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Equity</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">{{ $inr($equity) }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Total (Cr)</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">{{ $inr($liabs + $equity) }}</span>
                        </div>
                    </div>
                </div>

                {{-- BALANCE STATUS --}}
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800 shadow-sm">
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Balance Status</h2>
                    </div>
                    
                </div>
            </div>

            {{-- RIGHT COLUMN: CHARTS & SUMMARY --}}
            <div class="space-y-6">
				
				<h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Financial Analysis</h2>
                {{-- PIE CHARTS SECTION --}}
                <div class="space-y-6">
                    {{-- Assets vs Liabilities + Equity --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                        <h3 class="font-semibold mb-3 text-center">Assets vs Liabilities + Equity</h3>
                        <div class="h-64">
                            <canvas id="balanceSheetChart"></canvas>
                        </div>
                        <div class="mt-3 text-center text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex flex-col space-y-1">
                                <span class="flex items-center justify-center">
                                    <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-1"></span>
                                    Assets: ₹{{ number_format($assets, 2) }}
                                </span>
                                <span class="flex items-center justify-center">
                                    <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-1"></span>
                                    Liabilities: ₹{{ number_format($liabs, 2) }}
                                </span>
                                <span class="flex items-center justify-center">
                                    <span class="inline-block w-3 h-3 bg-purple-500 rounded-full mr-1"></span>
                                    Equity: ₹{{ number_format($equity, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Assets Breakdown --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                        <h3 class="font-semibold mb-3 text-center">Assets Breakdown</h3>
                        <div class="h-64">
                            <canvas id="assetsBreakdownChart"></canvas>
                        </div>
                        <div class="mt-3 text-center text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex flex-col space-y-1">
                                <span class="flex items-center justify-center">
                                    <span class="inline-block w-3 h-3 bg-teal-500 rounded-full mr-1"></span>
                                    Current Assets: ₹{{ number_format($currentAssets, 2) }}
                                </span>
                                <span class="flex items-center justify-center">
                                    <span class="inline-block w-3 h-3 bg-orange-500 rounded-full mr-1"></span>
                                    Fixed Assets: ₹{{ number_format($fixedAssets, 2) }}
                                </span>
                                <span class="flex items-center justify-center">
                                    <span class="inline-block w-3 h-3 bg-gray-500 rounded-full mr-1"></span>
                                    Other Assets: ₹{{ number_format($otherAssets, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Liabilities Breakdown --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                        <h3 class="font-semibold mb-3 text-center">Liabilities Breakdown</h3>
                        <div class="h-64">
                            <canvas id="liabilitiesBreakdownChart"></canvas>
                        </div>
                        <div class="mt-3 text-center text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex flex-col space-y-1">
                                <span class="flex items-center justify-center">
                                    <span class="inline-block w-3 h-3 bg-red-500 rounded-full mr-1"></span>
                                    Current Liabilities: ₹{{ number_format($currentLiabilities, 2) }}
                                </span>
                                <span class="flex items-center justify-center">
                                    <span class="inline-block w-3 h-3 bg-pink-500 rounded-full mr-1"></span>
                                    Long-term Liabilities: ₹{{ number_format($longTermLiabilities, 2) }}
                                </span>
                                <span class="flex items-center justify-center">
                                    <span class="inline-block w-3 h-3 bg-yellow-500 rounded-full mr-1"></span>
                                    Other Liabilities: ₹{{ number_format($otherLiabilities, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            {{-- SUMMARY CARDS --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="text-green-600 dark:text-green-400 text-sm font-medium">Total Assets</div>
                    <div class="text-2xl font-bold text-green-700 dark:text-green-300">
                        ₹{{ number_format($assets, 2) }}
                    </div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="text-blue-600 dark:text-blue-400 text-sm font-medium">Total Liabilities</div>
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                        ₹{{ number_format($liabs, 2) }}
                    </div>
                </div>

                <div
                    class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                    <div class="text-purple-600 dark:text-purple-400 text-sm font-medium">Total Equity</div>
                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-300">
                        ₹{{ number_format($equity, 2) }}
                    </div>
                </div>

                <div
                    class="{{ $balanceDiff === 0.0 ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800' }} border rounded-lg p-4">
                    <div
                        class="{{ $balanceDiff === 0.0 ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' }} text-sm font-medium">
                        Balance Status</div>
                    <div
                        class="text-2xl font-bold {{ $balanceDiff === 0.0 ? 'text-green-700 dark:text-green-300' : 'text-amber-700 dark:text-amber-300' }}">
                        {{ $balanceDiff === 0.0 ? 'Balanced' : 'Difference: ₹' . number_format(abs($balanceDiff), 2) }}
                    </div>
                </div>
            </div>

            {{-- DETAILED REPORT SECTION --}}
            

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

    {{-- Chart.js Library --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Initialize Pie Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Data from PHP
            const assets = {{ $assets ?? 0 }};
            const liabilities = {{ $liabs ?? 0 }};
            const equity = {{ $equity ?? 0 }};

            const currentAssets = {{ $currentAssets ?? 0 }};
            const fixedAssets = {{ $fixedAssets ?? 0 }};
            const otherAssets = {{ $otherAssets ?? 0 }};

            const currentLiabilities = {{ $currentLiabilities ?? 0 }};
            const longTermLiabilities = {{ $longTermLiabilities ?? 0 }};
            const otherLiabilities = {{ $otherLiabilities ?? 0 }};

            // Balance Sheet Overview Chart
            const balanceSheetCtx = document.getElementById('balanceSheetChart').getContext('2d');
            new Chart(balanceSheetCtx, {
                type: 'pie',
                data: {
                    labels: ['Assets', 'Liabilities', 'Equity'],
                    datasets: [{
                        data: [assets, liabilities, equity],
                        backgroundColor: ['#10B981', '#3B82F6', '#8B5CF6'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const total = assets + liabilities + equity;
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${context.label}: ₹${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Assets Breakdown Chart
            const assetsBreakdownCtx = document.getElementById('assetsBreakdownChart').getContext('2d');
            new Chart(assetsBreakdownCtx, {
                type: 'pie',
                data: {
                    labels: ['Current Assets', 'Fixed Assets', 'Other Assets'],
                    datasets: [{
                        data: [currentAssets, fixedAssets, otherAssets],
                        backgroundColor: ['#0D9488', '#F59E0B', '#6B7280'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const total = currentAssets + fixedAssets + otherAssets;
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) :
                                        '0.0';
                                    return `${context.label}: ₹${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Liabilities Breakdown Chart
            const liabilitiesBreakdownCtx = document.getElementById('liabilitiesBreakdownChart').getContext('2d');
            new Chart(liabilitiesBreakdownCtx, {
                type: 'pie',
                data: {
                    labels: ['Current Liabilities', 'Long-term Liabilities', 'Other Liabilities'],
                    datasets: [{
                        data: [currentLiabilities, longTermLiabilities, otherLiabilities],
                        backgroundColor: ['#EF4444', '#EC4899', '#F59E0B'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const total = currentLiabilities + longTermLiabilities +
                                        otherLiabilities;
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) :
                                        '0.0';
                                    return `${context.label}: ₹${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });

        // Original form handling script
        (function() {
            const sel = document.getElementById('rangeSel');
            const hidFrom = document.getElementById('from');
            const hidTo = document.getElementById('to');
            const form = sel.form;

            const cfWrap = document.getElementById('customFromWrap');
            const ctLbl = document.getElementById('customToLabel');
            const ctWrap = document.getElementById('customToWrap');
            const fromC = document.getElementById('from_custom');
            const toC = document.getElementById('to_custom');

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
                return {
                    from,
                    to
                };
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
                return {
                    from,
                    to
                };
            }

            function computeRange(kind) {
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

            function toggleCustom(show) {
                [cfWrap, ctLbl, ctWrap].forEach(el => el.classList.toggle('hidden', !show));
            }

            function applyPreset(kind) {
                const r = computeRange(kind);
                if (r) {
                    hidFrom.value = fmt(r.from);
                    hidTo.value = fmt(r.to);
                    if (!fromC.value) fromC.value = hidFrom.value;
                    if (!toC.value) toC.value = hidTo.value;
                }
            }

            function submitForm() {
                if (form.requestSubmit) {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            }

            sel.addEventListener('change', () => {
                const kind = sel.value;
                const isCustom = kind === 'custom';
                toggleCustom(isCustom);

                if (!isCustom) {
                    applyPreset(kind);
                    // Auto-submit for non-custom options
                    setTimeout(submitForm, 100);
                } else {
                    hidFrom.value = fromC.value || '';
                    hidTo.value = toC.value || '';
                    // Don't auto-submit for custom - let user choose dates
                }
            });

            // For custom dates, only submit when search button is clicked
            form.addEventListener('submit', (e) => {
                if (sel.value === 'custom') {
                    hidFrom.value = fromC.value || '';
                    hidTo.value = toC.value || '';
                } else {
                    applyPreset(sel.value);
                }
            });

            // Initialize on page load
            if (sel.value !== 'custom') {
                applyPreset(sel.value);
                toggleCustom(false);
            } else {
                toggleCustom(true);
            }
        })();
    </script>

@endsection
