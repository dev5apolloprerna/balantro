<?php $__env->startSection('title', 'Profit & Loss A/C'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $queryParams = array_merge(request()->query(), [
        'from' => request('from', $from ?? ''),
        'to' => request('to', $to ?? ''),
        'range' => request('range', $rangeSel ?? ''),
    ]);
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
    $periodText = function () use ($from, $to, $rangeSel) {
        $f = date('d-m-Y', strtotime($from ?? request('from')));
        $t = date('d-m-Y', strtotime($to ?? request('to')));
        if ($f || $t) {
            return trim(($f ?: '—') . ' to ' . ($t ?: '—'));
        }
        // Add more cases as needed
        return '';
    };
?>
<div class="mt-1 border-b border-gray-200 dark:border-gray-700 pb-1">
    <div class="flex flex-wrap lg:flex-nowrap items-center justify-between gap-4">
        <!-- Left : Client Name -->
        <div class="flex items-center gap-3 shrink-0">
            <div
                class="h-10 w-10 rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 text-white flex items-center justify-center font-bold">
                <?php echo e(strtoupper(substr($user->name ?? '',0,1))); ?>

            </div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                <?php echo e(strtoupper($user->name ?? '')); ?>

            </h1>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-2 flex-1">
            <?php echo $__env->make('admin.clients.reports.tabmanu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <!-- Right : FY + Back -->
        <div class="flex items-center gap-3 shrink-0">
            <!-- <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                <?php echo e($labelFY ?? ''); ?>

            </span> -->
            <a href="<?php echo e(url()->previous()); ?>" title="Go Back"
                class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700
                hover:border-[#f472b6] hover:shadow-[0_0_15px_#f472b6] hover:scale-105 hover:-translate-y-1">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </div>
</div>

<div class="dashboard-main-body">
    <div class="container py-3">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Profit & Loss</h1>
                <p class="text-xs text-black-500 dark:text-gray-400 mt-0.5">
                    • <?php echo e($periodText()); ?>

                </p>
            </div>
            <div>
                <?php
                    $queryParams = array_merge(request()->query(), [
                        'from' => request('from', $from ?? ''),
                        'to' => request('to', $to ?? ''),
                        'range' => request('range', $rangeSel ?? ''),
                        'guid' => $guid,
                    ]);
                ?>
                <a href="<?php echo e(route('reports.pl.pdf', $queryParams)); ?>" title="Export into PDF"
                    class="group btn inline-block relative text-white px-4 py-2 text-sm rounded-md border border-gray-700
                                transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1">
                    <i class="fas fa-file-pdf"></i>
                    
                </a>
                &nbsp;
                <a href="<?php echo e(route('reports.pl.excel', $queryParams)); ?>" title="Export into Excel"
                    class="group btn inline-block relative text-white px-4 py-2 text-sm rounded-md border border-gray-700
                                transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#34d399]
                                hover:shadow-[0_0_15px_#34d399]
                                hover:scale-105
                                hover:-translate-y-1">
                    <i class="fas fa-file-excel"></i>
                    
                </a>                
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('clients.reports.pnl', $guid ?? '')); ?>" id="filterForm"
            class="mt-2 rounded-lg p-2 flex flex-wrap items-end gap-3">
            <?php echo csrf_field(); ?>
            <?php
                $rangeSel = request('range', $rangeSel ?? ($financialYearOptions->first()['value'] ?? ''));
                $rangeOptions = $financialYearOptions->pluck('label', 'value')->all();
            ?>
            <div class="relative"
                x-data="{
                    open: false,
                    selected: <?php echo \Illuminate\Support\Js::from($rangeSel)->toHtml() ?>,
                    options: <?php echo \Illuminate\Support\Js::from($rangeOptions)->toHtml() ?>
                }">

                <label class="block text-xs text-gray-600 dark:text-gray-300 mb-1">
                    Date Range
                </label>

                <!-- Hidden input (for form submit) -->
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
                    focus:ring-2 focus:ring-[#22d3ee]
                    transition-all duration-300">

                    <span x-text="options[selected] || 'Select Year'"></span>
                </button>

                <!-- Arrow -->
                <div class="mt-2 pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-300">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>

                <!-- Dropdown -->
                <ul x-show="open" @click.outside="open = false"
                    x-transition
                    class="absolute z-50 mt-2 w-full rounded-xl overflow-hidden

                    bg-white/10 dark:bg-white/5
                    backdrop-blur-2xl

                    border border-white/20
                    ring-1 ring-white/10

                    shadow-[0_8px_40px_rgba(0,0,0,0.4)]"
                    style="backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);">

                    <?php $__empty_1 = true; $__currentLoopData = $financialYearOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $financialYear): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li>
                            <button type="button"
                                @click="selected = <?php echo \Illuminate\Support\Js::from($financialYear['value'])->toHtml() ?>; open = false; handleRangeChange(<?php echo \Illuminate\Support\Js::from($financialYear['value'])->toHtml() ?>)"
                                class="w-full text-left px-4 py-2 text-sm transition-all duration-200 text-gray-800 dark:text-white hover:bg-black/10 dark:hover:bg-white/10 hover:text-[#22d3ee]"
                                :class="selected === <?php echo \Illuminate\Support\Js::from($financialYear['value'])->toHtml() ?> ? 'bg-[#22d3ee]/20 text-[#22d3ee]' : ''">
                                <?php echo e($financialYear['label']); ?>

                            </button>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">No financial years found</li>
                    <?php endif; ?>
                    <li>
                        <button type="button"
                            @click="selected = 'custom'; open = false; handleRangeChange('custom')"
                            class="w-full text-left px-4 py-2 text-sm transition-all duration-200 text-gray-800 dark:text-white hover:bg-black/10 dark:hover:bg-white/10 hover:text-[#22d3ee]"
                            :class="selected === 'custom' ? 'bg-[#22d3ee]/20 text-[#22d3ee]' : ''">
                            Custom Date
                        </button>
                    </li>

                </ul>
            </div>
            <div id="customFromWrap" class="<?php echo e($rangeSel === 'custom' ? '' : 'hidden'); ?>">
                <label class="block text-xs text-black-600 dark:text-gray-300 mb-1">From Date</label>
                <input type="date" name="from_custom" id="from_custom" value="<?php echo e(request('from')); ?>" min="1900-01-01" max="2099-12-31"
                    class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
            </div>
            <div id="customToLabel"
                class="pb-2 text-black-500 dark:text-gray-400 <?php echo e($rangeSel === 'custom' ? '' : 'hidden'); ?>">TO</div>
            <div id="customToWrap" class="<?php echo e($rangeSel === 'custom' ? '' : 'hidden'); ?>">
                <label class="block text-xs text-black-600 dark:text-gray-300 mb-1">To Date</label>
                <input type="date" name="to_custom" id="to_custom" value="<?php echo e(request('to')); ?>" min="1900-01-01" max="2099-12-31"
                    class=" appearance-none bg-gradient-to-br from-white/50 to-white/20 dark:from-white/10 dark:to-transparent backdrop-blur-xl border border-gray-300/80 dark:border-cyan-400/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.4)] dark:shadow-[inset_0_1px_2px_rgba(255,255,255,0.05)] text-gray-900 dark:text-white rounded-xl px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-[#22d3ee] focus:border-[#22d3ee] focus:shadow-[0_0_12px_rgba(34,211,238,0.6)] transition-all duration-300">
            </div>

            <input type="hidden" name="from" id="from" value="<?php echo e(request('from')); ?>">
            <input type="hidden" name="to" id="to" value="<?php echo e(request('to')); ?>">

            <div class="flex gap-2 <?php echo e($rangeSel === 'custom' ? '' : 'hidden'); ?>"  id="searchBtn">
                <button type="submit"
                    class="rounded-md border border-gray-700 text-black dark:text-white  px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#22d3ee]
                                hover:shadow-[0_0_15px_#22d3ee]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">Search</button>
                <a href="<?php echo e(route('reports.pl')); ?>"
                    class="rounded-md border border-gray-700 text-black dark:text-white px-4 py-2 text-sm transition duration-1000 ease-in-out
                                transition-property: all;
                                hover:border-[#a78bfa]
                                hover:shadow-[0_0_15px_#a78bfa]
                                hover:scale-105
                                hover:-translate-y-1"
                                style="transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);">Reset</a>
            </div>
        </form>

        <?php if(isset($data['error'])): ?>
            <div class="alert alert-danger"><?php echo e($data['error']); ?></div>
        <?php endif; ?>

        
        <?php
            // ---------------- BASIC DATA ----------------
            $cr = $pl['cr'] ?? [];
            $dr = $pl['dr'] ?? [];
            $iInc = $pl['IndirectIncomes'] ?? [];
            $iExp = $pl['IndirectExpenses'] ?? [];
            
            // ---------------- SUM HELPER ----------------
            $sum = function (array $rows): float {
                $t = 0.0;
                foreach ($rows as $r) {
                    $t += (float) ($r['decMainAmount'] ?? 0);
                }
                return $t;
            };

            // ---------------- SALES & PURCHASES ----------------
            $salesAccounts = $sum(array_filter($cr, fn($item) => ($item['strGroupName'] ?? '') === 'Sales Accounts'));

            $directIncomes = $sum(array_filter($cr, fn($item) => ($item['strGroupName'] ?? '') === 'Direct Incomes'));

            $purchaseAccounts = $sum(
                array_filter($dr, fn($item) => ($item['strGroupName'] ?? '') === 'Purchase Accounts'),
            );

            $directExpenses = $sum(array_filter($dr, fn($item) => ($item['strGroupName'] ?? '') === 'Direct Expenses'));
            
            // ---------------- OPENING & CLOSING STOCK ----------------
            // Debug: Check what's in $pl
            // {{-- dd($pl) --}}

            // Try to get stock values from different possible locations
            $openingStock = 0.0;
            $closingStock = 0.0;

            // Method 1: Direct from $pl array (from service)
            $openingStock = (float) ($pl['OpeningStock'] ?? 0);
            $closingStock = (float) ($pl['ClosingStock'] ?? 0);

            // Method 2: If not found, check if there are separate stock entries
            if ($openingStock == 0 && $closingStock == 0) {
                // Check if there are stock rows in the data
                foreach ($cr as $row) {
                    if (($row['strGroupName'] ?? '') === 'Opening Stock') {
                        $openingStock = (float) ($row['decMainAmount'] ?? 0);
                    }
                    if (($row['strGroupName'] ?? '') === 'Closing Stock') {
                        $closingStock = (float) ($row['decMainAmount'] ?? 0);
                    }
                }

                foreach ($dr as $row) {
                    if (($row['strGroupName'] ?? '') === 'Opening Stock') {
                        $openingStock = (float) ($row['decMainAmount'] ?? 0);
                    }
                    if (($row['strGroupName'] ?? '') === 'Closing Stock') {
                        $closingStock = (float) ($row['decMainAmount'] ?? 0);
                    }
                }
            }

            // Method 3: Check if there's a separate stock array in $pl
            if (isset($pl['stocks'])) {
                foreach ($pl['stocks'] as $stock) {
                    if (($stock['strGroupName'] ?? '') === 'Opening Stock') {
                        $openingStock = (float) ($stock['decMainAmount'] ?? 0);
                    }
                    if (($stock['strGroupName'] ?? '') === 'Closing Stock') {
                        $closingStock = (float) ($stock['decMainAmount'] ?? 0);
                    }
                }
            }

            // Method 4: Try to get COGS from service
            $cogs = (float) ($pl['COGS'] ?? 0);

            // If COGS is available but stocks aren't, we can try to calculate
            if ($cogs > 0 && ($openingStock == 0 || $closingStock == 0)) {
                $purchasesOnly = 0;
                foreach ($dr as $row) {
                    if (in_array($row['strGroupName'] ?? '', ['Purchase Accounts', 'Direct Expenses'])) {
                        $purchasesOnly += (float) ($row['decMainAmount'] ?? 0);
                    }
                }

                $cogs = $openingStock + $purchasesOnly - $closingStock;
            }

            // ---------------- FORMATTER ----------------
            $fmt = function (float $n): string {
                return ($n < 0 ? '- ₹ ' : '₹ ') . number_format(abs($n), 2);
            };

            // ---------------- INDIRECT INCOME & EXPENSES ----------------
            $indirectIncome = $sum($iInc);
            $indirectExpenses = $sum($iExp);

            // ---------------- GET OTHER TOTALS FROM SERVICE ----------------
            $totalCr = (float) ($pl['totalCr'] ?? 0);
            $totalDr = (float) ($pl['totalDr'] ?? 0);
            $grossProfitLoss = (float) ($pl['GrossPandL'] ?? 0);
            $netProfit = (float) ($pl['NetPandL'] ?? 0);

            // ---------------- EXCEL FORMAT CALCULATIONS ----------------
            $totalIncome = $salesAccounts + $directIncomes + max($closingStock, 0) + max(-$openingStock, 0);
            $totalExpenses = max($openingStock, 0) + $purchaseAccounts + $directExpenses + abs(min($closingStock, 0));

            // Recalculate for consistency (or use from service)
            $grossProfitLoss = $totalIncome - $totalExpenses;
            $grossIsProfit = $grossProfitLoss >= 0;
            $grossAbs = abs($grossProfitLoss);

            $netProfit = $grossProfitLoss + $indirectIncome - $indirectExpenses;
            $netIsProfit = $netProfit >= 0;
            $netAbs = abs($netProfit);

            // Charts (always positive)
            $directCr = abs($directIncomes);
            $salesAccountsCr = abs($salesAccounts);

            $directDr = abs($directExpenses);
            $purchaseAccountsDr = abs($purchaseAccounts);

            $indirectCr = abs($sum($iInc));
            $indirectDr = abs($sum($iExp));
            
            $totalIncomeForCharts = $directCr + $indirectCr;
            $totalExpensesForCharts = $directDr + $indirectDr;
        ?>

        
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

            
            <div class="space-y-6">
                
                <div class="space-y-6">
                    
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden  shadow-sm">
                        <div class="px-4 py-3 bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] backdrop-blur-md  border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Income</h3>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            
                            <div
                                class="group flex px-4 py-3 justify-between items-center border-t border-b border-gray-100 dark:border-gray-700 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] transition-all duration-300">
                                <div class="flex items-center ">
                                    <a href="<?php echo e(route('reports.ledger', ['group_id' => collect($cr)->where('strGroupName', 'Sales Accounts')->first()['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')])); ?>"
                                        class="text-black dark:text-white group-hover:text-gray-900 dark:group-hover:text-black hover:underlin">
                                        Sales Accounts
                                    </a>
                                </div>
                                <span class="font-medium group-hover:text-gray-900 dark:group-hover:text-black"><?php echo e($fmt($salesAccounts)); ?></span>
                            </div>
                            
                            <div
                                class="group flex px-4 py-3 justify-between items-center border-t border-b border-gray-100 dark:border-gray-700 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] transition-all duration-300">
                                <div class="flex items-center">
                                    <a href="<?php echo e(route('reports.ledger', ['group_id' => collect($cr)->where('strGroupName', 'Direct Incomes')->first()['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')])); ?>"
                                        class="text-black dark:text-white group-hover:text-gray-900 dark:group-hover:text-black hover:underlin">
                                        Direct Incomes
                                    </a>
                                </div>
                                <span class="font-medium group-hover:text-gray-900 dark:group-hover:text-black"><?php echo e($fmt($directIncomes)); ?></span>
                            </div>
                            
                            <?php if($closingStock > 0): ?>
                                <div class="group flex px-4 py-3 justify-between items-center border-t border-b border-gray-100 dark:border-gray-700 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] transition-all duration-300">
                                    <div class="flex items-center">
                                        <span class="text-black dark:text-white group-hover:text-gray-900 dark:group-hover:text-black">Closing Stock</span>
                                    </div>
                                    <span class="font-medium group-hover:text-gray-900 dark:group-hover:text-black"><?php echo e($fmt($closingStock)); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if($openingStock < 0): ?>
                                <div class="group flex px-4 py-3 justify-between items-center border-t border-b border-gray-100 dark:border-gray-700 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] transition-all duration-300">
                                    <div class="flex items-center">
                                        <span class="text-black dark:text-white group-hover:text-gray-900 dark:group-hover:text-black">Opening Stock</span>
                                    </div>
                                    <span class="font-medium group-hover:text-gray-900 dark:group-hover:text-black"><?php echo e($fmt(abs($openingStock))); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div
                                class="flex justify-between px-4 py-3 bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] !mt-0 items-center border-t border-gray-300 dark:border-gray-600 pt-2 font-semibold">
                                <span>I. Total Income</span>
                                <span><?php echo e($fmt($totalIncome)); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden  shadow-sm ">
                        <div class="px-4 py-3 bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] backdrop-blur-md  border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Expenses</h3>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php if($openingStock > 0): ?>
                                <div class="group flex px-4 py-3 justify-between items-center border-t border-b border-gray-100 dark:border-gray-700 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] transition-all duration-300">
                                    <div class="flex items-center">
                                        <span class="text-black dark:text-white group-hover:text-gray-900 dark:group-hover:text-black">Opening Stock</span>
                                    </div>
                                    <span class="font-medium group-hover:text-gray-900 dark:group-hover:text-black"><?php echo e($fmt($openingStock)); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if($closingStock < 0): ?>
                                <div class="group flex px-4 py-3 justify-between items-center border-t border-b border-gray-100 dark:border-gray-700 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] transition-all duration-300">
                                    <div class="flex items-center">
                                        <span class="text-black dark:text-white group-hover:text-gray-900 dark:group-hover:text-black">Closing Stock</span>
                                    </div>
                                    <span class="font-medium group-hover:text-gray-900 dark:group-hover:text-black"><?php echo e($fmt(abs($closingStock))); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            
                            <div
                                class="group flex px-4 py-3 justify-between items-center border-t border-b border-gray-100 dark:border-gray-700 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] transition-all duration-300">
                                <div class="flex items-center">
                                    <a href="<?php echo e(route('reports.ledger', ['group_id' => collect($dr)->where('strGroupName', 'Purchase Accounts')->first()['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')])); ?>"
                                        class="text-black dark:text-white group-hover:text-gray-900 dark:group-hover:text-black hover:underlin">
                                        Purchase Accounts
                                    </a>
                                </div>
                                <span class="font-medium group-hover:text-gray-900 dark:group-hover:text-black"><?php echo e($fmt($purchaseAccounts)); ?></span>
                            </div>

                            
                            <div
                                class="group flex px-4 py-3 justify-between items-center border-t border-b border-gray-100 dark:border-gray-700 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] transition-all duration-300">
                                <div class="flex items-center">
                                    <a href="<?php echo e(route('reports.ledger', ['group_id' => collect($dr)->where('strGroupName', 'Direct Expenses')->first()['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')])); ?>"
                                        class="text-black dark:text-white group-hover:text-gray-900 dark:group-hover:text-black hover:underlin">
                                        Direct Expenses
                                    </a>
                                </div>
                                <span class="font-medium group-hover:text-gray-900 dark:group-hover:text-black"><?php echo e($fmt($directExpenses)); ?></span>
                            </div>

                            
                            <div
                                class="flex justify-between bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] !mt-0 px-4 py-3 items-center border-t border-gray-300 dark:border-gray-600 pt-2 font-semibold">
                                <span>II. Total Expenses</span>
                                <span><?php echo e($fmt($totalExpenses)); ?></span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="border border-gray-200 bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] dark:border-gray-700 rounded-xl shadow px-4 py-3">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-md font-semibold">
                                <span>III. Gross Profit / Loss (I - II)</span>
                                <span
                                    class="<?php echo e($grossIsProfit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'); ?>">
                                    <?php echo e($grossIsProfit ? '+' : '-'); ?><?php echo e($fmt($grossAbs)); ?>

                                </span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden  shadow-sm ">
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php $__currentLoopData = $iInc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                                <div
                                    class="group flex px-4 py-3 justify-between items-center border-t border-b border-gray-100 dark:border-gray-700 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] transition-all duration-300">
                                    <div class="flex items-center">
                                        <a href="<?php echo e(route('reports.ledger', ['group_id' => $row['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')])); ?>"
                                            class="text-black dark:text-white group-hover:text-gray-900 dark:group-hover:text-black hover:underlin">
                                            <?php echo e($row['strGroupName'] ?? '—'); ?>

                                        </a>
                                    </div>
                                    <span class="class="font-medium group-hover:text-gray-900 dark:group-hover:text-black""><?php echo e($fmt($row['decMainAmount'])); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div
                                class="flex justify-between bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] !mt-0 px-4 py-3 items-center border-t border-gray-300 dark:border-gray-600 pt-2 font-semibold">
                                <span>IV. Total Indirect Income</span>
                                <span><?php echo e($fmt($indirectIncome)); ?></span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden  shadow-sm ">
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php $__currentLoopData = $iExp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div
                                    class="group flex px-4 py-3 justify-between items-center border-t border-b border-gray-100 dark:border-gray-700 hover:bg-[#22d3ee]/80 dark:hover:bg-[#22d3ee]/80 hover:shadow-[0_0_20px_rgba(34,211,238,0.8)] transition-all duration-300">
                                    <div class="flex items-center">
                                        <a href="<?php echo e(route('reports.ledger', ['group_id' => $row['iPrimaryGroupId'] ?? null, 'from' => request('from'), 'to' => request('to')])); ?>"
                                            class="text-black dark:text-white group-hover:text-gray-900 dark:group-hover:text-black hover:underlin">
                                            <?php echo e($row['strGroupName'] ?? '—'); ?>

                                        </a>
                                    </div>
                                    
                                    <span class="text-md"><?php echo e($fmt((float) $row['decMainAmount'])); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div
                                class="flex justify-between bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] !mt-0 px-4 py-3 items-center border-t border-gray-300 dark:border-gray-600 pt-2 font-semibold">
                                <span>V. Total Indirect Expenses</span>
                                <span><?php echo e($fmt($indirectExpenses)); ?></span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="border border-gray-200 bg-[rgba(10,20,35,0.20)] dark:bg-[rgba(10,20,35,0.6)] dark:border-gray-700 rounded-xl shadow px-4 py-3">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-md font-semibold">
                                <span>VI. Net Profit / Loss (III + IV - V) </span>
                                <span
                                    class="<?php echo e($netIsProfit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'); ?>">
                                    <?php echo e($netIsProfit ? '+' : '-'); ?><?php echo e($fmt($netAbs)); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="space-y-6">
                <h2 class="text-[#22d3ee] font-semibold dark:text-[#22d3ee] mb-3">Profit & Loss Analysis</h2>

                
                <!-- <div class=" rounded-xl shadow p-4">
                    <h3 class="font-semibold mb-3 text-center">Income vs Expenses</h3>
                    <div class="h-64">
                        <canvas id="incomeExpenseChart"></canvas>
                    </div>
                    <div class="mt-3 text-center text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex flex-col space-y-2">
                            <span class="flex items-center justify-center">
                                <span class="inline-block w-3 h-3 bg-[#34d399] rounded-full mr-2"></span>
                                Income: <?php echo e($fmt($totalIncomeForCharts)); ?>

                            </span>
                            <span class="flex items-center justify-center">
                                <span class="inline-block w-3 h-3 bg-[#a78bfa] rounded-full mr-2"></span>
                                Expenses: <?php echo e($fmt($totalExpensesForCharts)); ?>

                            </span>
                        </div>
                    </div>
                </div> -->
                <div class="flex items-center gap-10">
                    <div class="w-[300px] h-[300px]">
                        <canvas id="plPie"></canvas>
                    </div>

                    <div id="plLegend" class="flex-1"></div>
                </div>

                
                <div class="space-y-3">

                    <!-- HEADER -->
                    <div class="flex items-center justify-between">
                        <h3 class="text-[#22d3ee] font-semibold dark:text-[#22d3ee] mb-3">Breakdown Analysis</h3>

                        <!-- <select id="breakdownType"
                            class="bg-[rgba(10,20,35,0.6)] backdrop-blur-md border border-cyan-500/20 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-cyan-400">
                            <option value="income">Income Breakdown</option>
                            <option value="expenses">Expenses Breakdown</option>
                        </select> -->
                        <div class="relative"
                            x-data="{
                                open: false,
                                selected: 'income',
                                options: {
                                    'income': 'Income Breakdown',
                                    'expenses': 'Expenses Breakdown'
                                },

                                init() {
                                    this.$watch('selected', value => {
                                        updateBreakdown(value);
                                    });
                                }
                            }">

                            <!-- Hidden input -->
                            <input type="hidden" id="breakdownType" :value="selected">

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
                                focus:ring-2 focus:ring-[#22d3ee]
                                transition-all duration-300">

                                <span x-text="options[selected]"></span>
                            </button>

                            <!-- Arrow -->
                            <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-300">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>

                            <!-- Dropdown -->
                            <ul x-show="open" @click.outside="open = false"
                                x-transition
                                class="absolute z-50 mt-2 w-full rounded-xl overflow-hidden
                                bg-white/10 dark:bg-white/5
                                backdrop-blur-2xl
                                border border-white/20
                                ring-1 ring-white/10
                                shadow-[0_8px_40px_rgba(0,0,0,0.4)]">

                                <!-- Income -->
                                <li>
                                    <button type="button"
                                        @click="selected = 'income'; open = false"
                                        class="w-full text-left px-4 py-2 text-sm
                                        text-gray-800 dark:text-white
                                        hover:bg-black/10 dark:hover:bg-white/10
                                        hover:text-[#22d3ee]"
                                        :class="selected === 'income'
                                            ? 'bg-[#22d3ee]/20 text-[#22d3ee]'
                                            : ''">
                                        Income Breakdown
                                    </button>
                                </li>

                                <!-- Expenses -->
                                <li>
                                    <button type="button"
                                        @click="selected = 'expenses'; open = false"
                                        class="w-full text-left px-4 py-2 text-sm
                                        text-gray-800 dark:text-white
                                        hover:bg-black/10 dark:hover:bg-white/10
                                        hover:text-[#22d3ee]"
                                        :class="selected === 'expenses'
                                            ? 'bg-[#22d3ee]/20 text-[#22d3ee]'
                                            : ''">
                                        Expenses Breakdown
                                    </button>
                                </li>

                            </ul>
                        </div>
                    </div>

                    <!-- CHART + LEGEND -->
                    <div class="flex items-center gap-10">

                        <div class="w-[300px] h-[300px]">
                            <canvas id="breakdownChart"></canvas>
                        </div>

                        <div id="breakdownLegend"
                            class="flex-1">
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
    
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script>
        const directCr = <?php echo e($directCr ?? 0); ?>;
        const salesAccountsCr = <?php echo e($salesAccountsCr ?? 0); ?>;
       
        const directDr = <?php echo e($directDr ?? 0); ?>;
        const purchaseAccounts = <?php echo e($purchaseAccounts ?? 0); ?>;
        const indirectCr = <?php echo e($indirectCr ?? 0); ?>;
        const indirectDr = <?php echo e($indirectDr ?? 0); ?>;
        //const totalIncome = <?php echo e($totalIncomeForCharts ?? 0); ?>;
        const totalExpenses = <?php echo e($totalExpensesForCharts ?? 0); ?>;

        // Chart instances
        let incomeExpenseChart, breakdownChart;
        // Initialize Pie Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Data from PHP - all variables are now properly defined
            

            // Income vs Expenses Chart
            // const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
            // incomeExpenseChart = new Chart(incomeExpenseCtx, {
            //     type: 'pie',
            //     data: {
            //         labels: ['Income', 'Expenses'],
            //         datasets: [{
            //             data: [totalIncome, totalExpenses],
            //             backgroundColor: ['#34d399', '#a78bfa'],
            //             borderWidth: 2,
            //             borderColor: '#fff'
            //         }]
            //     },
            //     options: {
            //         responsive: true,
            //         maintainAspectRatio: false,
                    
            //         plugins: {
            //             // legend: {
            //             //     position: 'bottom',
            //             //     labels: {
            //             //         padding: 20,
            //             //         usePointStyle: true,
            //             //         pointStyle: 'circle',
            //             //     }
            //             // },
            //             legend: {
            //                 display: false
            //             },
            //             tooltip: {
            //                 usePointStyle: true,
            //                 callbacks: {
            //                     label: function(context) {
            //                         const value = context.raw;
            //                         const total = context.dataset.data.reduce((a, b) => a + b, 0);
            //                         const percentage = ((value / total) * 100).toFixed(1);
            //                         return `${context.label}: ₹${value.toLocaleString()} (${percentage}%)`;
            //                     }
            //                 }
            //             }
            //         }
            //     }
            // });

            // Breakdown Chart
            const breakdowncolors = ['#22d3ee', '#f472b6','#fbbf24'];
            const breakdownCtx = document.getElementById('breakdownChart').getContext('2d');

            // Initial breakdown chart (Income Breakdown by default)
            breakdownChart = new Chart(breakdownCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Sales','Direct Income', 'Indirect Income'],
                    datasets: [{
                        data: [salesAccountsCr, directCr, indirectCr],
                        backgroundColor: (ctx) => {
                            const chart = ctx.chart;
                            const { ctx: c, chartArea } = chart;

                            if (!chartArea) return;

                            

                            return breakdowncolors.map(color => {
                                const gradient = c.createLinearGradient(
                                    0,
                                    chartArea.top,
                                    0,
                                    chartArea.bottom
                                );

                                gradient.addColorStop(0, color + 'FF');
                                gradient.addColorStop(0.4, color + 'AA');
                                gradient.addColorStop(0.7, color + '55');
                                gradient.addColorStop(1, color + '10');

                                return gradient;
                            });
                        },
                        borderColor: breakdowncolors, //'rgba(0,0,0,0.15)',
                        borderWidth: 1.5,
                    }]
                },
                
                cutout: '70%',
                radius: '90%',
                options: {
                    responsive: true,
                    //maintainAspectRatio: false,
                    maintainAspectRatio: true,
                    elements: {
                        line: {
                            borderWidth: 3,
                            tension: 0.45
                        },
                        point: {
                            radius: 0,
                            hoverRadius: 6
                        }
                    },
                    cutout: '65%',
                    radius: '85%',
                    layout: {
                        padding: 10
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
            renderBreakdownLegend(
                'breakdownLegend',
                ['Sales','Direct Income', 'Indirect Income'],
                [salesAccountsCr ,directCr, indirectCr],
                ['#22d3ee', '#f472b6','#fbbf24']
            );

            // Dropdown change handler
            // document.getElementById('breakdownType').addEventListener('change', function() {
            //     const breakdownType = this.value;
            //     const incomeLegend = document.getElementById('incomeLegend');
            //     const expensesLegend = document.getElementById('expensesLegend');

            //     if (breakdownType === 'income') {
            //         // Update to Income Breakdown
            //         breakdownChart.data.labels = ['Direct Income', 'Indirect Income'];
            //         breakdownChart.data.datasets[0].data = [directCr, indirectCr];
            //         breakdownChart.data.datasets[0].backgroundColor = ['#22d3ee', '#f472b6'];

            //         // Show income legend, hide expenses legend
            //         // incomeLegend.classList.remove('hidden');
            //         // expensesLegend.classList.add('hidden');
            //         renderBreakdownLegend('breakdownLegend',
            //             ['Direct Income', 'Indirect Income'],
            //             [directCr, indirectCr],
            //             ['#22d3ee', '#f472b6']
            //         );
            //     } else {
            //         // Update to Expenses Breakdown
            //         breakdownChart.data.labels = ['Direct Expenses', 'Indirect Expenses'];
            //         breakdownChart.data.datasets[0].data = [directDr, indirectDr];
            //         breakdownChart.data.datasets[0].backgroundColor = ['#fbbf24', '#f472b6'];

            //         // Show expenses legend, hide income legend
            //         // incomeLegend.classList.add('hidden');
            //         // expensesLegend.classList.remove('hidden');

            //         renderBreakdownLegend('breakdownLegend',
            //             ['Direct Expenses', 'Indirect Expenses'],
            //             [directDr, indirectDr],
            //             ['#fbbf24', '#f472b6']
            //         );
            //     }

            //     breakdownChart.update();
            // });
        });

        function updateBreakdown(type) {

            const colors = type === 'income'
                ? ['#22d3ee', '#f472b6','#fbbf24']
                : ['#fbbf24', '#f472b6','#22d3ee'];

            breakdownChart.data.labels = type === 'income'
                ? ['Sales', 'Direct Income', 'Indirect Income']
                : ['Purchase','Direct Expenses', 'Indirect Expenses'];

            breakdownChart.data.datasets[0].data = type === 'income'
                ? [salesAccountsCr,directCr, indirectCr]
                : [purchaseAccounts, directDr, indirectDr];

            // 🔥 APPLY SAME GRADIENT AS FIRST PIE
            breakdownChart.data.datasets[0].backgroundColor = (ctx) => {
                const chart = ctx.chart;
                const { ctx: c, chartArea } = chart;

                if (!chartArea) return;

                return colors.map(color => {
                    const gradient = c.createLinearGradient(
                        0,
                        chartArea.top,
                        0,
                        chartArea.bottom
                    );

                    gradient.addColorStop(0, color + 'FF');
                    gradient.addColorStop(0.4, color + 'AA');
                    gradient.addColorStop(0.7, color + '55');
                    gradient.addColorStop(1, color + '10');

                    return gradient;
                });
            };
            breakdownChart.data.datasets[0].borderColor = colors;

            renderBreakdownLegend(
                'breakdownLegend',
                breakdownChart.data.labels,
                breakdownChart.data.datasets[0].data,
                colors
            );

            breakdownChart.update();
        }
       
        let plData = <?php echo json_encode($pl ?? [], 15, 512) ?>;

        // ===== SAFE FIND FUNCTION =====
        const getValue = (arr, name) => {
            return Number(arr?.find(x => x.strGroupName === name)?.decMainAmount || 0);
        };

        // ===== CORE VALUES =====
        const sales = getValue(plData.cr, 'Sales Accounts');
        const directIncome = getValue(plData.cr, 'Direct Incomes');

        const purchase = getValue(plData.dr, 'Purchase Accounts');
        const directExpense = getValue(plData.dr, 'Direct Expenses');

        const indirectIncome = Number(plData.IndirectIncomes?.[0]?.decMainAmount || 0);
        const indirectExpense = Number(plData.IndirectExpenses?.[0]?.decMainAmount || 0);

        const openingStock = Number(plData.OpeningStock || 0);
        const closingStock = Number(plData.ClosingStock || 0);
        const cogs = openingStock + purchase - closingStock;
        // ✅ USE SERVICE VALUE (MOST IMPORTANT)
        //const cogs = Number(plData.COGS || 0);

        // ===== FINAL CALCULATION (MATCH BLADE) =====
        const totalIncome = sales + directIncome + closingStock;
        const totalExpense = openingStock + purchase + directExpense;

        const gross = totalIncome - totalExpense;
        const net = gross + indirectIncome - indirectExpense;

        const isProfit = net >= 0;
        const colors = [
            '#22d3ee',
            '#a78bfa',
            '#fbbf24',
            '#f472b6',
            '#d47171',
            '#a5ef44',
            isProfit ? '#22c55e' : '#ef4444'
        ];
        // ===== PROFIT & LOSS PIE =====
        new Chart(document.getElementById('plPie'), {
            type: 'doughnut',
            data: {
                labels: [
                    'Revenue',
                    'Cost of Revenue',
                    'Direct Income',
                    'indirect Income',
                    'Direct Expense',
                    'Indirect Expense',
                    isProfit ? 'Profit' : 'Loss' // 🔥 ADD
                ],
                // labels: plLabels,
                datasets: [{
                    data: [
                        sales,
                        cogs,
                        directIncome,
                        indirectIncome,
                        directExpense,
                        indirectExpense,
                        Math.abs(net)
                    ],
                    // data: plValues,
                    // backgroundColor: [
                    //     '#22d3ee', // neon green
                    //     '#a78bfa', // neon red
                    //     '#fbbf24', // neon orange
                    //     '#f472b6', // neon purple
                    //     isProfit ? '#22c55e' : '#ef4444' // 🔥 ADD
                    // ]
                    backgroundColor: (ctx) => {
                    const chart = ctx.chart;
                    const {
                        ctx: c,
                        chartArea
                    } = chart;
                    if (!chartArea) return;

                    return colors.map(color => {
                        const gradient = c.createLinearGradient(0, chartArea.top, 0,
                            chartArea.bottom);

                        gradient.addColorStop(0, color + 'FF'); // bright top
                        gradient.addColorStop(0.4, color + 'AA'); // mid glow
                        gradient.addColorStop(0.7, color + '55'); // fade
                        gradient.addColorStop(1, color + '10'); // glass bottom

                        return gradient;
                    });
                },
                borderColor: colors, //  'rgba(0,0,0,0.15)', 
                    borderWidth: 1.5,
                }]
            },
            
            cutout: '70%',
            radius: '90%',
            options: {
                responsive: true,
                //maintainAspectRatio: false,
                maintainAspectRatio: true,
                elements: {
                    line: {
                        borderWidth: 3,
                        tension: 0.45
                    },
                    point: {
                        radius: 0,
                        hoverRadius: 6
                    }
                },
                cutout: '65%',
                radius: '85%',
                layout: {
                    padding: 10
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

         const centerText = {
            id: 'centerText',
            beforeDraw(chart) {
                const {
                    ctx,
                    width,
                    height
                } = chart;

                ctx.restore();

                const net = totalIncome - totalExpense;
                const isProfit = net >= 0;

                ctx.font = 'bold 14px sans-serif';
                ctx.fillStyle = isProfit ? '#22c55e' : '#ef4444';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';

                ctx.fillText(isProfit ? 'Net Profit' : 'Net Loss', width / 2, height / 2 - 10);

                ctx.font = 'bold 18px sans-serif';
                ctx.fillText(
                    '₹ ' + Math.abs(net).toLocaleString('en-IN'),
                    width / 2,
                    height / 2 + 15
                );

                ctx.save();
            }
        };

        let plRaw = [
            {
                label: 'Revenue',
                value: sales,
                color: '#22d3ee'
            },
            {
                label: 'Cost of Revenue',
                value: cogs,
                color: '#a78bfa'
            },
            {
                label: 'Direct Income',
                value: directIncome,
                color: '#fbbf24'
            },
            {
                label: 'Indirect Income',
                value: indirectIncome,
                color: '#f472b6'
            },
            {
                label: 'Direct Expense',
                value: directExpense,
                color: '#d47171'
            },
            {
                label: 'Indirect Expense',
                value: indirectExpense,
                color: '#a5ef44'
            },
            {
                label: isProfit ? 'Profit' : 'Loss',
                value: Math.abs(net),
                color: isProfit ? '#22c55e' : '#ef4444'
            }
        ];

        // ✅ REMOVE 0 VALUES
        //plRaw = plRaw.filter(x => Number(x.value) == 0);

        let plLabels = plRaw.map(x => x.label);
        let plValues = plRaw.map(x => x.value);
        let plColors = plRaw.map(x => x.color);

        renderLegend('plLegend', plLabels, plValues, plColors);

        // function renderLegend(containerId, labels, values, colors) {

        //     let html = '';

        //     labels.forEach((label, i) => {

        //         let value = Number(values[i] || 0);

        //         html += `
        //             <div class="flex items-center gap-4">

        //                 <!-- LEFT -->
        //                 <div class="flex items-center gap-2 min-w-0">
        //                     <span style="
        //                         width:10px;
        //                         height:10px;
        //                         border-radius:50%;
        //                         background:${colors[i]};
        //                         display:inline-block;
        //                     "></span>

        //                     <span class="truncate text-gray-700 dark:text-gray-300">
        //                         ${label}
        //                     </span>
        //                 </div>

        //                 <!-- RIGHT -->
        //                 <div class="text-right font-medium text-white whitespace-nowrap">
        //                     ₹ ${value.toLocaleString('en-IN')}
        //                 </div>

        //             </div>
        //             `;
        //         });

        //     document.getElementById(containerId).innerHTML = html;
        // }

        function renderLegend(containerId, labels, values, colors) {

            let html = '';

            labels.forEach((label, i) => {

                let value = Math.round(Number(values[i] || 0));
                // ✅ HIDE ZERO VALUES
                if (value == 0) return;
                html += `
                    <div class="flex flex-col gap-1">

                        <!-- LEFT -->
                        <div class="flex items-center gap-2 min-w-0">
                            <span style="
                                width:10px;
                                height:10px;
                                border-radius:50%;
                                background:${colors[i]};
                                display:inline-block;
                            "></span>

                            <span class="truncate text-black-700 dark:text-gray-300">
                                ${label}
                            </span>
                        </div>

                        <!-- RIGHT -->
                        <div class="pl-5 font-medium text-black dark:text-white">
                            ₹ ${value.toLocaleString('en-IN')}
                        </div>

                    </div>
                `;
                }); // 9687796077

            document.getElementById(containerId).innerHTML = html;
        }

        function renderBreakdownLegend(containerId, labels, values, colors) {
            let html = '';

            labels.forEach((label, i) => {

                let value = Math.round(Number(values[i] || 0));
                 // ✅ HIDE ZERO VALUES
                if (value <= 0) return;
                html += `
                    <div class="flex flex-col gap-1">

                        <!-- LEFT -->
                        <div class="flex items-center gap-2 min-w-0">
                            <span style="
                                width:10px;
                                height:10px;
                                border-radius:50%;
                                background:${colors[i]};
                                display:inline-block;
                            "></span>

                            <span class="truncate text-black-700 dark:text-gray-300">
                                ${label}
                            </span>
                        </div>

                        <!-- RIGHT -->
                        <div class="pl-5 font-medium text-black dark:text-white">
                            ₹ ${value.toLocaleString('en-IN')}
                        </div>

                    </div>
                `;
                });

            document.getElementById(containerId).innerHTML = html;
        }

        function handleRangeChange(value) {
            document.querySelector('input[name="range"]').value = value;

            const hidFrom = document.getElementById('from');
            const hidTo = document.getElementById('to');
            const financialYearRanges = <?php echo json_encode($financialYearOptions->keyBy('value'), 15, 512) ?>;
            const selectedRange = financialYearRanges[value];

            const fromCustom = document.getElementById('from_custom');
            const toCustom = document.getElementById('to_custom');

            const cfWrap = document.getElementById('customFromWrap');
            const ctWrap = document.getElementById('customToWrap');

            const pad = n => String(n).padStart(2, '0');
            const fmt = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

            function fyStartYearFor(date) {
                return date.getMonth() >= 3 ? date.getFullYear() : date.getFullYear() - 1;
            }

            function fyRange(startYr) {
                return {
                    from: new Date(startYr, 3, 1),
                    to: new Date(startYr + 1, 2, 31)
                };
            }

            function computeRange(kind) {
                const now = new Date();
                const fyStartYr = fyStartYearFor(now);

                if (kind === 'current_year') return fyRange(fyStartYr);
                if (kind === 'last_year') return fyRange(fyStartYr - 1);

                return null;
            }

            const isCustom = value === 'custom';
            
            // 🔥 UI toggle
            cfWrap.classList.toggle('hidden', !isCustom);
            ctWrap.classList.toggle('hidden', !isCustom);
            document.getElementById('customToLabel')
                .classList.toggle('hidden', !isCustom);
            document.getElementById('searchBtn')
                .classList.toggle('hidden', !isCustom);
            if (!isCustom) {

                // ✅ CLEAR CUSTOM INPUTS (MAIN FIX)
                fromCustom.value = '';
                toCustom.value = '';

                // ✅ SET NEW RANGE
                const r = selectedRange ? {
                    from: new Date(`${selectedRange.from}T00:00:00`),
                    to: new Date(`${selectedRange.to}T00:00:00`)
                } : computeRange(value);
                if (r) {
                    hidFrom.value = fmt(r.from);
                    hidTo.value = fmt(r.to);
                }

                document.getElementById('filterForm').submit();

            } else {

                // custom mode
                hidFrom.value = '';
                hidTo.value = '';
            }
        }

        document.getElementById('filterForm').addEventListener('submit', function(e) {

            const selected = document.querySelector('input[name="range"]').value;
            
            if (selected === 'custom') {
                const toC = document.getElementById('to_custom');
                const fromC = document.getElementById('from_custom');

                if (!fromC.value || !toC.value) {
                    e.preventDefault();
                    alert('Select both dates');
                    return;
                }

                // ✅ Balance Sheet logic
                document.getElementById('from').value = fromC.value;   // NOT needed
                document.getElementById('to').value = toC.value;
            }
        });

        function isValidDate(dateString) {

            const parts = dateString.split('/');

            if (parts.length !== 3) return false;

            const day = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10);
            const year = parseInt(parts[2], 10);

            // JS month starts from 0
            const date = new Date(year, month - 1, day);

            return (
                date.getFullYear() === year &&
                date.getMonth() === month - 1 &&
                date.getDate() === day
            );
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/clients/reports/pl.blade.php ENDPATH**/ ?>