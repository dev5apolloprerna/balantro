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

            // Calculate all values
            $directCr = $sum($cr, false);
            $directDr = $sum($dr, true);
            $gross = $directCr - $directDr;
            $grossIsProfit = $gross >= 0;
            $grossAbs = abs($gross);

            $indirectCr = $sum($iInc, false);
            $indirectDr = $sum($iExp, true);

            // NET calculation
            $net = ($grossIsProfit ? $grossAbs : -$grossAbs) + ($indirectCr - $indirectDr);
            $netIsProfit = $net >= 0;
            $netAbs = abs($net);

            // Totals for tables
            $tradingDebitTotal = $directDr + ($grossIsProfit ? $grossAbs : 0);
            $tradingCreditTotal = $directCr + (!$grossIsProfit ? $grossAbs : 0);
            $tradingTotal = max($tradingDebitTotal, $tradingCreditTotal);

            $plDebitTotal = $indirectDr + (!$grossIsProfit ? $grossAbs : 0) + ($netIsProfit ? $netAbs : 0);
            $plCreditTotal = $indirectCr + ($grossIsProfit ? $grossAbs : 0) + (!$netIsProfit ? $netAbs : 0);
            $plTotal = max($plDebitTotal, $plCreditTotal);

            // For charts
            $totalIncome = $directCr + $indirectCr;
            $totalExpenses = $directDr + $indirectDr;
        @endphp

        

        {{-- MAIN CONTENT: SIDE BY SIDE LAYOUT --}}
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- LEFT COLUMN: REPORT DATA --}}
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">P &amp; L Report</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Period: {{ $from }} to {{ $to }}</p>
                    </div>
                </div>

                {{-- TRADING ACCOUNT --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Trading Account</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                            <h4 class="font-semibold mb-3 text-red-600 dark:text-red-400">Purchase A/c — Debit (Dr)</h4>
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 dark:text-gray-400">
                                        <th class="py-2">Particulars</th>
                                        <th class="py-2 text-right">Amount (₹)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($dr as $row)
                                        <tr>
                                            <td class="py-2">
                                                <a href="{{ route('reports.ledger', ['group_id' => $row['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                                    class="text-blue-600 hover:underline dark:text-blue-400">
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
                                    <tr class="border-t font-semibold dark:border-gray-600">
                                        <td class="py-2">Total</td>
                                        <td class="py-2 text-right">{{ $fmt($tradingTotal) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                            <h4 class="font-semibold mb-3 text-green-600 dark:text-green-400">Sales A/c — Credit (Cr)</h4>
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 dark:text-gray-400">
                                        <th class="py-2">Particulars</th>
                                        <th class="py-2 text-right">Amount (₹)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($cr as $row)
                                        <tr>
                                            <td class="py-2">
                                                <a href="{{ route('reports.ledger', ['group_id' => $row['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                                    class="text-blue-600 hover:underline dark:text-blue-400">
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
                                    <tr class="border-t font-semibold dark:border-gray-600">
                                        <td class="py-2">Total</td>
                                        <td class="py-2 text-right">{{ $fmt($tradingTotal) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- PROFIT & LOSS ACCOUNT --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Profit & Loss Account</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                            <h4 class="font-semibold mb-3 text-red-600 dark:text-red-400">P&L A/c — Debit (Dr)</h4>
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 dark:text-gray-400">
                                        <th class="py-2">Particulars</th>
                                        <th class="py-2 text-right">Amount (₹)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($iExp as $row)
                                        <tr>
                                            <td class="py-2">
                                                <a href="{{ route('reports.ledger', ['group_id' => $row['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                                    class="text-blue-600 hover:underline dark:text-blue-400">
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
                                    <tr class="border-t font-semibold dark:border-gray-600">
                                        <td class="py-2">Total</td>
                                        <td class="py-2 text-right">{{ $fmt($plTotal) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                            <h4 class="font-semibold mb-3 text-green-600 dark:text-green-400">P&L A/c — Credit (Cr)</h4>
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 dark:text-gray-400">
                                        <th class="py-2">Particulars</th>
                                        <th class="py-2 text-right">Amount (₹)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($iInc as $row)
                                        <tr>
                                            <td class="py-2">
                                                <a href="{{ route('reports.ledger', ['group_id' => $row['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')]) }}"
                                                    class="text-blue-600 hover:underline dark:text-blue-400">
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
                                    <tr class="border-t font-semibold dark:border-gray-600">
                                        <td class="py-2">Total</td>
                                        <td class="py-2 text-right">{{ $fmt($plTotal) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: GRAPHS --}}
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
                                Income: ₹{{ number_format($totalIncome, 2) }}
                            </span>
                            <span class="flex items-center justify-center">
                                <span class="inline-block w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                Expenses: ₹{{ number_format($totalExpenses, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Income Breakdown Chart --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <h3 class="font-semibold mb-3 text-center">Income Breakdown</h3>
                    <div class="h-64">
                        <canvas id="incomeBreakdownChart"></canvas>
                    </div>
                    <div class="mt-3 text-center text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex flex-col space-y-2">
                            <span class="flex items-center justify-center">
                                <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                                Direct Income: ₹{{ number_format($directCr, 2) }}
                            </span>
                            <span class="flex items-center justify-center">
                                <span class="inline-block w-3 h-3 bg-purple-500 rounded-full mr-2"></span>
                                Indirect Income: ₹{{ number_format($indirectCr, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Expenses Breakdown Chart --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <h3 class="font-semibold mb-3 text-center">Expenses Breakdown</h3>
                    <div class="h-64">
                        <canvas id="expensesBreakdownChart"></canvas>
                    </div>
                    <div class="mt-3 text-center text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex flex-col space-y-2">
                            <span class="flex items-center justify-center">
                                <span class="inline-block w-3 h-3 bg-orange-500 rounded-full mr-2"></span>
                                Direct Expenses: ₹{{ number_format($directDr, 2) }}
                            </span>
                            <span class="flex items-center justify-center">
                                <span class="inline-block w-3 h-3 bg-pink-500 rounded-full mr-2"></span>
                                Indirect Expenses: ₹{{ number_format($indirectDr, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FINAL SUMMARY SECTION --}}
        <!-- <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-gray-100">Financial Summary</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Performance Overview --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Performance Overview</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Total Revenue</span>
                            <span class="font-semibold text-green-600 dark:text-green-400">
                                ₹{{ number_format($totalIncome, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Total Expenses</span>
                            <span class="font-semibold text-red-600 dark:text-red-400">
                                ₹{{ number_format($totalExpenses, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Gross {{ $grossIsProfit ? 'Profit' : 'Loss' }}</span>
                            <span class="font-semibold {{ $grossIsProfit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                ₹{{ number_format($grossAbs, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Net {{ $netIsProfit ? 'Profit' : 'Loss' }}</span>
                            <span class="font-semibold {{ $netIsProfit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                ₹{{ number_format($netAbs, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Profitability Analysis --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Profitability Analysis</h3>
                    <div class="space-y-4">
                        @php
                            $grossProfitMargin = $totalIncome > 0 ? ($grossIsProfit ? $grossAbs : -$grossAbs) / $totalIncome * 100 : 0;
                            $netProfitMargin = $totalIncome > 0 ? ($netIsProfit ? $netAbs : -$netAbs) / $totalIncome * 100 : 0;
                            $expenseRatio = $totalIncome > 0 ? $totalExpenses / $totalIncome * 100 : 0;
                        @endphp
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Gross Profit Margin</span>
                            <span class="font-semibold {{ $grossProfitMargin >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ number_format($grossProfitMargin, 2) }}%
                            </span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Net Profit Margin</span>
                            <span class="font-semibold {{ $netProfitMargin >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ number_format($netProfitMargin, 2) }}%
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Expense to Income Ratio</span>
                            <span class="font-semibold text-blue-600 dark:text-blue-400">
                                {{ number_format($expenseRatio, 2) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Final Result Card --}}
            <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl shadow p-6 border border-blue-200 dark:border-blue-800">
                <div class="text-center">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        Financial Period Result
                    </h3>
                    <p class="text-lg {{ $netIsProfit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-semibold mb-2">
                        {{ $netIsProfit ? 'Profit Making Period' : 'Loss Making Period' }}
                    </p>
                    <p class="text-3xl font-bold {{ $netIsProfit ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                        {{ $netIsProfit ? '₹' . number_format($netAbs, 2) . ' Profit' : '₹' . number_format($netAbs, 2) . ' Loss' }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        Period: {{ $from }} to {{ $to }}
                    </p>
                </div>
            </div>
        </div> -->

        {{-- SUMMARY CARDS --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="text-green-600 dark:text-green-400 text-sm font-medium">Total Income</div>
                <div class="text-2xl font-bold text-green-700 dark:text-green-300">
                    ₹{{ number_format($totalIncome, 2) }}
                </div>
            </div>

            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="text-red-600 dark:text-red-400 text-sm font-medium">Total Expenses</div>
                <div class="text-2xl font-bold text-red-700 dark:text-red-300">
                    ₹{{ number_format($totalExpenses, 2) }}
                </div>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="text-blue-600 dark:text-blue-400 text-sm font-medium">Gross
                    {{ $grossIsProfit ? 'Profit' : 'Loss' }}</div>
                <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                    ₹{{ number_format($grossAbs, 2) }}
                </div>
            </div>

            <div
                class="{{ $netIsProfit ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' }} border rounded-lg p-4">
                <div
                    class="{{ $netIsProfit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} text-sm font-medium">
                    Net {{ $netIsProfit ? 'Profit' : 'Loss' }}</div>
                <div
                    class="text-2xl font-bold {{ $netIsProfit ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                    ₹{{ number_format($netAbs, 2) }}
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
            const totalIncome = directCr + indirectCr;
            const totalExpenses = directDr + indirectDr;

            // Income vs Expenses Chart
            const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
            new Chart(incomeExpenseCtx, {
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

            // Income Breakdown Chart
            const incomeBreakdownCtx = document.getElementById('incomeBreakdownChart').getContext('2d');
            new Chart(incomeBreakdownCtx, {
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

            // Expenses Breakdown Chart
            const expensesBreakdownCtx = document.getElementById('expensesBreakdownChart').getContext('2d');
            new Chart(expensesBreakdownCtx, {
                type: 'pie',
                data: {
                    labels: ['Direct Expenses', 'Indirect Expenses'],
                    datasets: [{
                        data: [directDr, indirectDr],
                        backgroundColor: ['#F59E0B', '#EC4899'],
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
        });

        // Form handling script with auto-submit
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