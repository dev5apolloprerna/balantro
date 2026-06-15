@extends('layouts.super_admin')
@section('title', 'Profit & Loss A/C')

@section('content')
    <div class="container py-3">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Profit & Loss A/C</h1>
            @php
                $queryParams = array_merge(request()->query(), [
                    'from' => request('from', $from ?? ''),
                    'to' => request('to', $to ?? ''),
                    'range' => request('range', $rangeSel ?? ''),
                ]);
            @endphp
            <div>
                <a href="{{ route('reports.pl.pdf', $queryParams) }}" title="Export into PDF"
                    class="btn btn-danger bg-red-600 text-white px-4 py-2 text-sm hover:bg-red-700">
                    <i class="fas fa-file-pdf"></i>
                </a>
                &nbsp;
                <a href="{{ route('reports.pl.excel', $queryParams) }}" title="Export into Excel"
                    class="btn btn-success bg-green-600 text-white px-4 py-2 text-sm hover:bg-green-700">
                    <i class="fas fa-file-excel"></i>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('reports.pl') }}"
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
                <a href="{{ route('reports.pl') }}"
                    class="rounded-md bg-red-600 text-white px-4 py-2 text-sm hover:bg-red-700">Reset</a>
            </div>
        </form>

        @isset($data['error'])
            <div class="alert alert-danger">{{ $data['error'] }}</div>
        @endisset

        {{-- Calculate all variables first to avoid undefined errors --}}
        @php
            // Initialize variables with default values
            $cr = $pl['cr'] ?? [];
            $dr = $pl['dr'] ?? [];
            $iInc = $pl['IndirectIncomes'] ?? [];
            $iExp = $pl['IndirectExpenses'] ?? [];

            // Get stock values from the data
            $openingStock = (float) ($pl['OpeningStock'] ?? 0);
            $closingStock = (float) ($pl['ClosingStock'] ?? 0);
            $cogs = (float) ($pl['COGS'] ?? 0);

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

            // Calculate values according to Excel format
            $salesAccounts = $sum(
                array_filter($cr, function ($item) {
                    return ($item['strGroupName'] ?? '') === 'Sales Accounts';
                }),
                false,
            );

            $directIncomes = $sum(
                array_filter($cr, function ($item) {
                    return ($item['strGroupName'] ?? '') === 'Direct Incomes';
                }),
                false,
            );

            $purchaseAccounts = $sum(
                array_filter($dr, function ($item) {
                    return ($item['strGroupName'] ?? '') === 'Purchase Accounts';
                }),
                true,
            );

            $directExpenses = $sum(
                array_filter($dr, function ($item) {
                    return ($item['strGroupName'] ?? '') === 'Direct Expenses';
                }),
                true,
            );

            $indirectIncome = $sum($iInc, false);
            $indirectExpenses = $sum($iExp, true);

            // Excel format calculations
            $totalIncome = $salesAccounts + $directIncomes + $closingStock; // A + B + C = D
            $totalExpenses = $openingStock + $purchaseAccounts + $directExpenses; // E + F + G = H

            // Gross Profit/Loss calculation (Excel format)
            $grossProfitLoss = $totalIncome - $totalExpenses; // D - H = I
            $grossIsProfit = $grossProfitLoss >= 0;
            $grossAbs = abs($grossProfitLoss);

            // Net Profit calculation (Excel format)
            $netProfit = $grossProfitLoss + $indirectIncome - $indirectExpenses; // I + J - K = L
            $netIsProfit = $netProfit >= 0;
            $netAbs = abs($netProfit);

            // For charts (keep existing chart calculations)
            $directCr = $sum($cr, false);
            $directDr = $sum($dr, true);
            $indirectCr = $sum($iInc, false);
            $indirectDr = $sum($iExp, true);

            // For charts (existing calculations)
            $totalIncomeForCharts = $directCr + $indirectCr;
            $totalExpensesForCharts = $directDr + $indirectDr;
        @endphp

        {{-- STOCK INFORMATION CARD --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="text-blue-600 dark:text-blue-400 text-sm font-medium">Opening Stock</div>
                <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                    {{ $fmt($openingStock) }}
                </div>
                <div class="text-xs text-blue-500 dark:text-blue-400 mt-1">As of {{ $from ?? 'Start Date' }}</div>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="text-green-600 dark:text-green-400 text-sm font-medium">Closing Stock</div>
                <div class="text-2xl font-bold text-green-700 dark:text-green-300">
                    {{ $fmt($closingStock) }}
                </div>
                <div class="text-xs text-green-500 dark:text-green-400 mt-1">As of {{ $to ?? 'End Date' }}</div>
            </div>

            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
                <div class="text-orange-600 dark:text-orange-400 text-sm font-medium">Cost of Goods Sold</div>
                <div class="text-2xl font-bold text-orange-700 dark:text-orange-300">
                    {{ $fmt($cogs) }}
                </div>
                <div class="text-xs text-orange-500 dark:text-orange-400 mt-1">Opening + Purchases - Closing</div>
            </div>
        </div>

        {{-- MAIN CONTENT: SIDE BY SIDE LAYOUT --}}
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- LEFT COLUMN: EXCEL FORMAT P&L REPORT --}}
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">P &amp; L Report</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Period: {{ $from }} to
                            {{ $to }}</p>
                    </div>
                </div>

                {{-- EXCEL FORMAT PROFIT & LOSS ACCOUNT --}}
                <div class="space-y-6">
                    {{-- INCOME SECTION --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Income</h3>

                        <div class="space-y-4">
                            {{-- Sales Accounts --}}
                            <div
                                class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                                <div class="flex items-center">
                                    <a href="{{ route('reports.ledger', ['group_id' => collect($cr)->where('strGroupName', 'Sales Accounts')->first()['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                        class="text-blue-600 hover:underline dark:text-blue-400">
                                        Sales Accounts
                                    </a>
                                </div>
                                <span class="font-medium">{{ $fmt($salesAccounts) }}</span>
                            </div>

                            {{-- Direct Incomes --}}
                            <div
                                class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                                <div class="flex items-center">
                                    <a href="{{ route('reports.ledger', ['group_id' => collect($cr)->where('strGroupName', 'Direct Incomes')->first()['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                        class="text-blue-600 hover:underline dark:text-blue-400">
                                        Direct Incomes
                                    </a>
                                </div>
                                <span class="font-medium">{{ $fmt($directIncomes) }}</span>
                            </div>

                            {{-- Closing Stock --}}
                            <div
                                class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                                <span>Closing Stock</span>
                                <span class="font-medium">{{ $fmt($closingStock) }}</span>
                            </div>

                            {{-- Total Income --}}
                            <div
                                class="flex justify-between items-center border-t-2 border-gray-300 dark:border-gray-600 pt-2 font-semibold">
                                <span>Total</span>
                                <span>{{ $fmt($totalIncome) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- EXPENSES SECTION --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Expenses</h3>

                        <div class="space-y-4">
                            {{-- Opening Stock --}}
                            <div
                                class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                                <span>Opening Stock</span>
                                <span class="font-medium">{{ $fmt($openingStock) }}</span>
                            </div>

                            {{-- Purchase Accounts --}}
                            <div
                                class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                                <div class="flex items-center">
                                    <a href="{{ route('reports.ledger', ['group_id' => collect($dr)->where('strGroupName', 'Purchase Accounts')->first()['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                        class="text-blue-600 hover:underline dark:text-blue-400">
                                        Purchase Accounts
                                    </a>
                                </div>
                                <span class="font-medium">{{ $fmt($purchaseAccounts) }}</span>
                            </div>

                            {{-- Direct Expenses --}}
                            <div
                                class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                                <div class="flex items-center">
                                    <a href="{{ route('reports.ledger', ['group_id' => collect($dr)->where('strGroupName', 'Direct Expenses')->first()['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                        class="text-blue-600 hover:underline dark:text-blue-400">
                                        Direct Expenses
                                    </a>
                                </div>
                                <span class="font-medium">{{ $fmt($directExpenses) }}</span>
                            </div>

                            {{-- Total Expenses --}}
                            <div
                                class="flex justify-between items-center border-t-2 border-gray-300 dark:border-gray-600 pt-2 font-semibold">
                                <span>Total</span>
                                <span>{{ $fmt($totalExpenses) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- GROSS PROFIT/LOSS SECTION --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                        {{-- <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Gross Profit/Loss</h3> --}}

                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-lg font-semibold">
                                <span>Gross Profit/Loss</span>
                                <span
                                    class="{{ $grossIsProfit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $grossIsProfit ? '+' : '-' }}{{ $fmt($grossAbs) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- INDIRECT INCOME SECTION --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                        {{-- <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Indirect Income</h3> --}}

                        <div class="space-y-3">
                            @foreach ($iInc as $row)
                                <div
                                    class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-1">
                                    <div class="flex items-center">
                                        <a href="{{ route('reports.ledger', ['group_id' => $row['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                            class="text-blue-600 hover:underline dark:text-blue-400 text-sm">
                                            {{ $row['strGroupName'] ?? '—' }}
                                        </a>
                                    </div>
                                    <span class="text-sm">{{ $fmt((float) $row['decMainAmount']) }}</span>
                                </div>
                            @endforeach
                            <div
                                class="flex justify-between items-center border-t border-gray-200 dark:border-gray-600 pt-2 font-medium">
                                <span>Total Indirect Income</span>
                                <span>{{ $fmt($indirectIncome) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- INDIRECT EXPENSES SECTION --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                        {{-- <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Indirect Expenses</h3> --}}

                        <div class="space-y-3">
                            @foreach ($iExp as $row)
                                <div
                                    class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-1">
                                    <div class="flex items-center">
                                        <a href="{{ route('reports.ledger', ['group_id' => $row['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                            class="text-blue-600 hover:underline dark:text-blue-400 text-sm">
                                            {{ $row['strGroupName'] ?? '—' }}
                                        </a>
                                    </div>
                                    <span class="text-sm">{{ $fmt(abs((float) $row['decMainAmount'])) }}</span>
                                </div>
                            @endforeach
                            <div
                                class="flex justify-between items-center border-t border-gray-200 dark:border-gray-600 pt-2 font-medium">
                                <span>Total Indirect Expenses</span>
                                <span>{{ $fmt($indirectExpenses) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- NET PROFIT/LOSS SECTION --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                        {{-- <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Net Profit/Loss</h3> --}}

                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-xl font-bold">
                                <span>Net Profit</span>
                                <span
                                    class="{{ $netIsProfit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $netIsProfit ? '+' : '-' }}{{ $fmt($netAbs) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: GRAPHS (KEEP EXISTING) --}}
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Financial Analysis</h2>

                {{-- Income vs Expenses Pie Chart --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <h3 class="font-semibold mb-3 text-center">Income vs Expenses</h3>
                    <div class="h-64">
                        <canvas id="incomeExpenseChart"></canvas>
                    </div>
                    <div class="mt-3 text-center text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex flex-col space-y-2">
                            <span class="flex items-center justify-center">
                                <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                Income: {{ $fmt($totalIncomeForCharts) }}
                            </span>
                            <span class="flex items-center justify-center">
                                <span class="inline-block w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                Expenses: {{ $fmt($totalExpensesForCharts) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Breakdown Chart with Dropdown --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-center">Breakdown Analysis</h3>
                        <select id="breakdownType"
                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                            <option value="income">Income Breakdown</option>
                            <option value="expenses">Expenses Breakdown</option>
                        </select>
                    </div>
                    <div class="h-64">
                        <canvas id="breakdownChart"></canvas>
                    </div>
                    <div class="mt-3 text-center text-sm text-gray-600 dark:text-gray-400">
                        <div id="incomeLegend" class="flex flex-col space-y-2">
                            <span class="flex items-center justify-center">
                                <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                                Direct Income: {{ $fmt($directCr) }}
                            </span>
                            <span class="flex items-center justify-center">
                                <span class="inline-block w-3 h-3 bg-purple-500 rounded-full mr-2"></span>
                                Indirect Income: {{ $fmt($indirectCr) }}
                            </span>
                        </div>
                        <div id="expensesLegend" class="hidden flex-col space-y-2">
                            <span class="flex items-center justify-center">
                                <span class="inline-block w-3 h-3 bg-orange-500 rounded-full mr-2"></span>
                                Direct Expenses: {{ $fmt($directDr) }}
                            </span>
                            <span class="flex items-center justify-center">
                                <span class="inline-block w-3 h-3 bg-pink-500 rounded-full mr-2"></span>
                                Indirect Expenses: {{ $fmt($indirectDr) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="text-green-600 dark:text-green-400 text-sm font-medium">Total Income</div>
                <div class="text-2xl font-bold text-green-700 dark:text-green-300">
                    {{ $fmt($totalIncome) }}
                </div>
            </div>

            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="text-red-600 dark:text-red-400 text-sm font-medium">Total Expenses</div>
                <div class="text-2xl font-bold text-red-700 dark:text-red-300">
                    {{ $fmt($totalExpenses) }}
                </div>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="text-blue-600 dark:text-blue-400 text-sm font-medium">Gross
                    {{ $grossIsProfit ? 'Profit' : 'Loss' }}</div>
                <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                    {{ $fmt($grossAbs) }}
                </div>
            </div>

            <div
                class="{{ $netIsProfit ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' }} border rounded-lg p-4">
                <div
                    class="{{ $netIsProfit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} text-sm font-medium">
                    Net {{ $netIsProfit ? 'Profit' : 'Loss' }}
                </div>
                <div
                    class="text-2xl font-bold {{ $netIsProfit ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                    {{ $fmt($netAbs) }}
                </div>
            </div>
        </div>

    </div>

    {{-- Chart.js Library --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Initialize Pie Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Data from PHP - all variables are now properly defined
            const directCr = {{ $directCr ?? 0 }};
            const directDr = {{ $directDr ?? 0 }};
            const indirectCr = {{ $indirectCr ?? 0 }};
            const indirectDr = {{ $indirectDr ?? 0 }};
            const totalIncome = {{ $totalIncomeForCharts ?? 0 }};
            const totalExpenses = {{ $totalExpensesForCharts ?? 0 }};

            // Chart instances
            let incomeExpenseChart, breakdownChart;

            // Income vs Expenses Chart
            const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
            incomeExpenseChart = new Chart(incomeExpenseCtx, {
                type: 'pie',
                data: {
                    labels: ['Income', 'Expenses'],
                    datasets: [{
                        data: [totalIncome, totalExpenses],
                        backgroundColor: ['#10B981', '#EF4444'],
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
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${context.label}: ₹${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Breakdown Chart
            const breakdownCtx = document.getElementById('breakdownChart').getContext('2d');

            // Initial breakdown chart (Income Breakdown by default)
            breakdownChart = new Chart(breakdownCtx, {
                type: 'pie',
                data: {
                    labels: ['Direct Income', 'Indirect Income'],
                    datasets: [{
                        data: [directCr, indirectCr],
                        backgroundColor: ['#3B82F6', '#8B5CF6'],
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
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${context.label}: ₹${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Dropdown change handler
            document.getElementById('breakdownType').addEventListener('change', function() {
                const breakdownType = this.value;
                const incomeLegend = document.getElementById('incomeLegend');
                const expensesLegend = document.getElementById('expensesLegend');

                if (breakdownType === 'income') {
                    // Update to Income Breakdown
                    breakdownChart.data.labels = ['Direct Income', 'Indirect Income'];
                    breakdownChart.data.datasets[0].data = [directCr, indirectCr];
                    breakdownChart.data.datasets[0].backgroundColor = ['#3B82F6', '#8B5CF6'];

                    // Show income legend, hide expenses legend
                    incomeLegend.classList.remove('hidden');
                    expensesLegend.classList.add('hidden');
                } else {
                    // Update to Expenses Breakdown
                    breakdownChart.data.labels = ['Direct Expenses', 'Indirect Expenses'];
                    breakdownChart.data.datasets[0].data = [directDr, indirectDr];
                    breakdownChart.data.datasets[0].backgroundColor = ['#F59E0B', '#EC4899'];

                    // Show expenses legend, hide income legend
                    incomeLegend.classList.add('hidden');
                    expensesLegend.classList.remove('hidden');
                }

                breakdownChart.update();
            });
        });

        // Form handling script with auto-submit (keep existing)
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
                    setTimeout(submitForm, 100);
                } else {
                    hidFrom.value = fromC.value || '';
                    hidTo.value = toC.value || '';
                }
            });

            form.addEventListener('submit', (e) => {
                if (sel.value === 'custom') {
                    hidFrom.value = fromC.value || '';
                    hidTo.value = toC.value || '';
                } else {
                    applyPreset(sel.value);
                }
            });

            if (sel.value !== 'custom') {
                applyPreset(sel.value);
                toggleCustom(false);
            } else {
                toggleCustom(true);
            }
        })();
    </script>

@endsection
