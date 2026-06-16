<?php $__env->startSection('title', 'Financial Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\DB;

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

        // Define color mapping for groups
        $colorMap = [
            'blue' => 'bg-blue-500',
            'amber' => 'bg-amber-500',
            'violet' => 'bg-violet-500',
            'fuchsia' => 'bg-fuchsia-500',
            'emerald' => 'bg-emerald-500',
            'rose' => 'bg-rose-500',
            'teal' => 'bg-teal-500',
            'indigo' => 'bg-indigo-500',
            'purple' => 'bg-purple-500',
            'pink' => 'bg-pink-500',
            'yellow' => 'bg-yellow-500',
            'orange' => 'bg-orange-500',
            'lime' => 'bg-lime-500',
            'cyan' => 'bg-cyan-500',
        ];

        // Get selected groups from session or use default
        $userId = auth()->id();
        $sessionKey = "user_{$userId}_selected_groups";

        // Ensure $allGroups is properly defined
        $allGroups = $allGroups ?? collect();

        // Default groups - make sure this exists
        $defaultGroupIds = $defaultGroupIds ?? $allGroups->pluck('iGroupId')->take(8)->toArray();

        // Get selected groups from session - USE THE UPDATED DATA FROM CONTROLLER
        $selectedGroups = $selectedGroups ?? session($sessionKey, $defaultGroupIds);

        // REMOVE THE 8-GROUP LIMIT - Allow up to 20 groups
        // if (count($selectedGroups) > 8) {
        // $selectedGroups = array_slice($selectedGroups, 0, 8);
        // session([$sessionKey => $selectedGroups]);
        // }

        // Get selected groups with balances from controller
        $selectedGroupsWithBalances = $selectedGroupsWithBalances ?? [];
        
        // If we don't have balances data, create it from allGroups
        if (empty($selectedGroupsWithBalances)) {
            $selectedGroupsWithBalances = $allGroups
                ->whereIn('iGroupId', $selectedGroups)
                ->map(function ($group) {
                    return [
                        'iGroupId' => (int) $group->iGroupId,
                        'strGroupName' => $group->strGroupName,
                        'Closing' => (float) ($group->Closing ?? 0),
                        'Opening' => (float) ($group->Opening ?? 0),
                    ];
                })
                ->values()
                ->toArray();
        }

        // Convert to JSON for JavaScript
        $selectedGroupsJson = json_encode($selectedGroups);
        $allGroupIdsJson = json_encode($allGroups->pluck('iGroupId')->toArray());
        $defaultGroupIdsJson = json_encode($defaultGroupIds);
        
        $payload = $data ?? ($bsData ?? []);
        $rows = collect($payload['rows'] ?? []);

        $drRows = $rows->where('Side', 'DR');
        $crRows = $rows->where('Side', 'CR');

        $displayAmountDr = fn($v) => abs((float) $v);
        $displayAmount = fn($v) => (float) $v;

        $totalAssets = 0;
        $totalCr = 0;
        $liabs = 0;
        $equity = 0;
        foreach ($drRows as $r) {
            //$totalAssets += abs((float)$r->decMainAmount);
            $amount = (float) ($r->decMainAmount ?? 0);
            if ($amount > 0) {
                $totalAssets += -1 * $r->decMainAmount;
            } else {
                $totalAssets += $displayAmountDr($r->decMainAmount ?? 0);
            }
        }
        $currentLiabilities = 0;
        $longTermLiabilities = 0;
        $otherLiabilities = 0;
        $Profit_Loss_Amt = 0;
        foreach ($crRows as $r) {
            $amt = (float) $r->decMainAmount;
            $totalCr += $amt;        
            if (in_array($r->strGroupName, ['Capital Account', 'Profit & Loss A/c'])) {
                $equity += $amt;
                if($r->strGroupName == 'Profit & Loss A/c') {
                    $Profit_Loss_Amt = $amt;
                }            
            } else {
                $liabs += $amt;
            }
            
            if (str_contains($r->strGroupName, 'Current Liabilities')) {
                $currentLiabilities += $amt;
            } elseif (str_contains($r->strGroupName, 'Loans')) {
                $longTermLiabilities += $amt;
            } elseif (!in_array($r->strGroupName, ['Capital Account', 'Profit & Loss A/c'])) {
                    $otherLiabilities += $amt;
                }
            }
            $assets = $totalAssets;
        $fmt = fn($v) => number_format((float) $v, 2, '.', ',');
    ?>

    <style>
        /* ===== 6 COLOR SYSTEM ===== */
        .color-0 { --card: #22d3ee; }
        /* cyan */
        .color-1 { --card: #a78bfa; }
        /* violet */
        .color-2 { --card: #34d399; }
        /* mint */
        .color-3 { --card: #fbbf24; }
        /* yellow */
        .color-4 { --card: #f472b6; }
        /* pink */
        .color-5 { --card: #60a5fa; }
        /* blue */
        /* Smooth base transition */
        .card-hover>div { transition: all 0.3s ease;}

        /* ===== UPDATED HOVER EFFECT ===== */
        .card-hover:hover>div {
            /* softer shadow from all sides */
            box-shadow:
                0 8px 20px rgba(0, 0, 0, 0.06),
                0 0 12px var(--card);
            transform: translateY(-3px);
            /* 🔥 border color change */
            border-color: var(--card) !important;
        }

        /* ===== LABEL COLOR CHANGE ===== */
        .card-hover:hover .text-\[12px\] {
            color: var(--card) !important;
        }

        /* ===== OPTIONAL: smoother label transition ===== */
        .text-\[12px\] {
            transition: color 0.3s ease;
        }

        /* ===== ICON COLOR SYSTEM ===== */
        .color-0 .bg-blue-50,
        .color-0 .bg-cyan-50 {
            background-color: rgba(34, 211, 238, 0.1);
            color: #22d3ee;
        }

        .color-1 .bg-violet-50 {
            background-color: rgba(167, 139, 250, 0.1);
            color: #a78bfa;
        }

        .color-2 .bg-green-50,
        .color-2 .bg-emerald-50 {
            background-color: rgba(52, 211, 153, 0.1);
            color: #34d399;
        }

        .color-3 .bg-yellow-50,
        .color-3 .bg-amber-50 {
            background-color: rgba(251, 191, 36, 0.1);
            color: #fbbf24;
        }

        .color-4 .bg-pink-50,
        .color-4 .bg-rose-50 {
            background-color: rgba(244, 114, 182, 0.1);
            color: #f472b6;
        }

        .color-5 .bg-blue-50,
        .color-5 .bg-sky-50,
        .color-5 .bg-indigo-50 {
            background-color: rgba(96, 165, 250, 0.1);
            color: #60a5fa;
        }

        /* CYAN */
        .color-0 .rounded-full {
            background-color: rgba(34, 211, 238, 0.12) !important;
            color: #22d3ee !important;
        }

        /* VIOLET */
        .color-1 .rounded-full {
            background-color: rgba(167, 139, 250, 0.12) !important;
            color: #a78bfa !important;
        }

        /* MINT */
        .color-2 .rounded-full {
            background-color: rgba(52, 211, 153, 0.12) !important;
            color: #34d399 !important;
        }

        /* YELLOW */
        .color-3 .rounded-full {
            background-color: rgba(251, 191, 36, 0.12) !important;
            color: #fbbf24 !important;
        }

        /* PINK */
        .color-4 .rounded-full {
            background-color: rgba(244, 114, 182, 0.12) !important;
            color: #f472b6 !important;
        }

        /* BLUE */
        .color-5 .rounded-full {
            background-color: rgba(96, 165, 250, 0.12) !important;
            color: #60a5fa !important;
        }

        .chart-container {
            position: relative;
            width: 100%;
            height: 100%;
        }

        #plLegend,
        #bsLegend {
            max-width: 220px;
        }

        #plLegend div,
        #bsLegend div {
            font-size: 13px;
            line-height: 1.4;
        }
        
    </style>

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
                <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                    <?php echo e($labelFY ?? ''); ?>

                </span>
                <a href="<?php echo e(url()->previous()); ?>" title="Go Back"
                   class="group btn inline-block relative text-black dark:text-white px-4 py-2 text-sm rounded-md border border-gray-700
                    hover:border-[#f472b6] hover:shadow-[0_0_15px_#f472b6] hover:scale-105 hover:-translate-y-1">
                    <i class="fa-solid fa-arrow-left"></i>                    
                </a>
            </div>
        </div>
    </div>

    <div class="dashboard-main-body">
        
        <div class="container">			
            <!-- <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Financial Dashboard</h1> 
                <div class="flex items-center gap-3">
                    <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($labelFY ?? ''); ?></div>
                    <a href="<?php echo e(url()->previous()); ?>"
                        class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium
                               bg-gray-200 text-gray-700 hover:bg-gray-300
                               dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Go Back
                    </a>
                </div>
            </div> -->

            
            <form id="graphForm" method="GET" action="<?php echo e(route('clients.dashboard', $guid ?? '')); ?>"
                class="mt-1">

                <div class="flex items-center gap-2">
                    <!-- <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Financial Year
                    </label> -->
                    <?php
                        $isLastFY = $fromVal === $lastStart->format('Y-m-d') && $toVal === $lastEnd->format('Y-m-d');
                        $isCurrentFY = $fromVal === $currStart->format('Y-m-d') && $toVal === $currEnd->format('Y-m-d');
                        
                    ?>
                    <!-- selected: '<?php echo e($isLastFY ? 'last_year' : 'current_year'); ?>', -->
                    <div class="relative"
                        x-data="{
                            open: false,
                            
                            selected: '<?php echo e($fyRangeSel  ?? 'current_year'); ?>',
                            options: {
                                'current_year': 'Current Year',
                                'last_year': 'Last Year'
                            },

                            init() {
                                this.$watch('selected', value => {
                                    handleRangeChange(value);
                                });
                            }
                        }">
                    <div class="flex items-center gap-2 mb-1">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Financial Year</label>
                        <div class="relative w-44">
                            <!-- Hidden input -->
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
                                focus:ring-2 focus:ring-[#22d3ee]">

                                <span x-text="options[selected]"></span>
                            </button>

                            <!-- Arrow -->
                            <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                                <i class="fa-solid fa-chevron-down text-xs" style="color: #00c2ff !important"></i>
                            </div>

                            <!-- Dropdown -->
                            <ul x-show="open" @click.outside="open = false"
                                x-transition
                                class="absolute z-50 mt-2 w-full rounded-xl overflow-hidden
                                bg-white/10 dark:bg-white/5 backdrop-blur-2xl border border-white/20">

                                <li>
                                    <button type="button"
                                        @click="selected='current_year'; open=false"
                                        class="w-full px-4 py-2 text-left hover:text-[#22d3ee]">
                                        Current Year
                                    </button>
                                </li>

                                <li>
                                    <button type="button"
                                        @click="selected='last_year'; open=false"
                                        class="w-full px-4 py-2 text-left hover:text-[#22d3ee]">
                                        Last Year
                                    </button>
                                </li>

                                

                            </ul>
                        </div>
                    </div>
                    </div>

                    <!-- KEEP THESE hidden fields -->
                    <!-- <option value="custom">Custom Range</option> -->
                    <input type="hidden" id="fy_from" name="from" value="<?php echo e($fromVal); ?>">
                    <input type="hidden" id="fy_to" name="to" value="<?php echo e($toVal); ?>">
                    <input type="hidden" id="fy_type" name="type" value="<?php echo e((int) ($activeType ?? 1)); ?>">
                </div>
            </form>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="h-[320px] flex flex-col">
                    <h3 class="text-[#22d3ee] font-semibold dark:text-[#22d3ee]">Profit & Loss</h3>
                    <div class="flex items-center  h-full">
                        <!-- LEFT: CHART -->
                        <div style="width:180px; height:180px;">
                            <canvas id="plPie"></canvas>
                        </div>
                        <!-- RIGHT: LEGEND -->
                        <div id="plLegend" class="ml-7 text-gray-700 dark:text-white text-sm w-full space-y-2"></div>
                    </div>
                </div>

                <div class="h-[320px] flex flex-col">
                    <h3 class="text-[#22d3ee] font-semibold dark:text-[#22d3ee]">Balance Sheet</h3>
                    <div class="flex items-center h-full">
                        <div style="width:180px; height:180px;">
                            <canvas id="bsPie"></canvas>
                        </div>
                        <div id="bsLegend" class="ml-7 text-gray-700 dark:text-white text-sm w-full space-y-2"></div>
                    </div>
                </div>

                <div class="h-[320px] bg-transparent flex flex-col">

                    <div class="flex justify-between items-center">
                        <h3 id="chartTitle" class="text-[#22d3ee] font-semibold dark:text-[#22d3ee]">Sales</h3>

                        <div class="relative"
                            x-data="{
                                open: false,
                                selected: 'sales',
                                options: {
                                    'sales': 'Sales',
                                    'purchase': 'Purchase',
                                    'direct_income': 'Direct Income',
                                    'indirect_income': 'Indirect Income',
                                    'direct_expense': 'Direct Expense',
                                    'indirect_expense': 'Indirect Expense'
                                },
                                // 🔥 MODIFY HERE
                                init() {
                                    this.$watch('selected', value => {
                                        document.getElementById('typeSelect').value = value;
                                        if (!isChartInitialized) return;
                                        handleTypeChange(value);
                                    });
                                }
                            }">

                            <!-- Hidden input -->
                            <input type="hidden" id="typeSelect" :value="selected">

                            <!-- Button -->
                            <button type="button" @click="open = !open"
                                class="text-left
                                bg-gradient-to-br from-white/60 to-white/30
                                dark:from-white/10 dark:to-transparent
                                backdrop-blur-xl
                                border border-gray-300/80 dark:border-cyan-400/20
                                text-gray-900 dark:text-white
                                rounded-xl px-3 py-1 pr-8 text-sm
                                focus:outline-none
                                focus:ring-2 focus:ring-[#22d3ee]">

                                <span x-text="options[selected]"></span>
                            </button>

                            <!-- Arrow -->
                            <div class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-gray-500">
                                <i class="fa-solid fa-chevron-down text-xs" style="color: #00c2ff !important"></i>
                            </div>

                            <!-- Dropdown -->
                            <ul x-show="open" @click.outside="open = false"
                                x-transition
                                class="absolute right-0 z-[9999] mt-2 w-56 rounded-xl
                                bg-[#0b0f19]/95 backdrop-blur-xl bg-white/10 dark:bg-white/5
                                border border-cyan-400/20 shadow-2xl
                                max-h-70 overflow-y-auto">

                                <template x-for="(label, key) in options" :key="key">
                                    <li>
                                        <button type="button"
                                            @click="selected = key; open = false"
                                            class="w-full px-4 py-2 text-left hover:text-[#22d3ee]">
                                            <span x-text="label"></span>
                                        </button>
                                    </li>
                                </template>

                            </ul>
                        </div>
                    </div>

                    <div class="flex-1">
                        <!-- <div style="height:320px; position:relative;">
                            <canvas id="salesBar"></canvas>
                        </div> -->                
                        <div class="chart-container">
                            <canvas id="salesBar"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"> -->
            <div class="flex flex-wrap items-center justify-end gap-3">

                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            View:
                        </label>

                        <div class="relative"
                            x-data="{
                                open:false,
                                selected:'chart',
                                options:{
                                    chart:'Chart',
                                    advanced:'Advance Chart'
                                },
                                init(){
                                    this.$watch('selected', val=>{
                                        if (!isChartInitialized) return;
                                        handleViewChange(val);
                                    })
                                }
                            }">

                            <input type="hidden" id="viewType" :value="selected">

                            <!-- Button -->
                            <button type="button" @click="open=!open"
                                class="text-left
                                bg-gradient-to-br from-white/60 to-white/30
                                dark:from-white/10 dark:to-transparent
                                backdrop-blur-xl
                                border border-gray-300/80 dark:border-cyan-400/20
                                text-gray-900 dark:text-white
                                rounded-xl px-3 py-1 pr-8 text-sm
                                focus:outline-none
                                focus:ring-2 focus:ring-[#22d3ee]">

                                <span x-text="options[selected]"></span>
                            </button>

                            <!-- Arrow -->
                            <div class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-gray-500">
                                <i class="fa-solid fa-chevron-down text-xs" style="color: #00c2ff !important"></i>
                            </div>

                            <!-- Dropdown -->
                            <ul x-show="open" @click.outside="open=false"
                                x-transition
                                class="absolute right-0 z-[9999] mt-2 w-48 rounded-xl
                                bg-[#0b0f19]/95 backdrop-blur-xl bg-white/10 dark:bg-white/5
                                border border-cyan-400/20 shadow-2xl">

                                <template x-for="(label,key) in options">
                                    <li>
                                        <button @click="selected=key; open=false"
                                            class="w-full px-4 py-2 text-left hover:text-[#22d3ee]">
                                            <span x-text="label"></span>
                                        </button>
                                    </li>
                                </template>

                            </ul>
                        </div>

                    </div>
                </div>
                <!-- Metric and Compare selectors on the right but aligned left within their container -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Metric Selector -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Metric:</label>
                    
                        <div class="relative"
                            x-data="metricDropdown()"
                            x-init="init()">

                            <input type="hidden" id="metricSelect" :value="selected">

                            <button type="button" @click="open=!open"
                                class="text-left
                                bg-gradient-to-br from-white/60 to-white/30
                                dark:from-white/10 dark:to-transparent
                                backdrop-blur-xl
                                border border-gray-300/80 dark:border-cyan-400/20
                                text-gray-900 dark:text-white
                                rounded-xl px-3 py-1 pr-8 text-sm
                                focus:outline-none
                                focus:ring-2 focus:ring-[#22d3ee]">

                                <span x-text="options[selected] || 'Select'"></span>
                            </button>

                            <div class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-gray-500">
                                <i class="fa-solid fa-chevron-down text-xs" style="color: #00c2ff !important"></i>
                            </div>

                            <ul x-show="open" @click.outside="open=false"
                                x-transition 
                                class="absolute right-0 z-[9999] mt-2 w-56 rounded-xl
                                bg-[#0b0f19]/95 backdrop-blur-xl bg-white/10 dark:bg-white/5
                                border border-cyan-400/20 shadow-2xl">

                                <template x-for="(label,key) in options">
                                    <li>
                                        <button @click="selected=key; open=false;"
                                            class="w-full px-4 py-2 text-left hover:text-[#22d3ee]">
                                            <span x-text="label"></span>
                                        </button>
                                    </li>
                                </template>

                            </ul>
                        </div>
                        
                    </div>

                    <!-- Comparison Selector -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Compare:</label>
                    
                        <div class="relative"
                            x-data="{
                                open:false,
                                selected:'none',
                                options:{
                                    'none':'None',
                                    'prev-month':'Previous Month',
                                    'prev-quarter':'Previous Quarter',
                                    'prev-year':'Previous Year'
                                },
                                init(){
                                    this.$watch('selected', val=>{
                                        document.getElementById('compareSelect').value = val;
                                        if (!isChartInitialized) return;
                                        renderChartFor(
                                            activeType,
                                            document.getElementById('metricSelect').value,
                                            val
                                        );
                                    })
                                }
                            }">

                            <input type="hidden" id="compareSelect" :value="selected">

                            <button type="button" @click="open=!open"
                                class="text-left
                                bg-gradient-to-br from-white/60 to-white/30
                                dark:from-white/10 dark:to-transparent
                                backdrop-blur-xl 
                                border border-gray-300/80 dark:border-cyan-400/20
                                text-gray-900 dark:text-white
                                rounded-xl px-3 py-1 pr-8 text-sm
                                focus:outline-none
                                focus:ring-2 focus:ring-[#22d3ee]">

                                <span x-text="options[selected]"></span>
                            </button>

                            <div class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-gray-500">
                                <i class="fa-solid fa-chevron-down text-xs" style="color: #00c2ff !important"></i>
                            </div>

                            <ul x-show="open" @click.outside="open=false"
                                x-transition
                                class="absolute right-0 z-[9999] mt-2 w-56 rounded-xl
                                bg-[#0b0f19]/95 backdrop-blur-xl bg-white/10 dark:bg-white/5
                                border border-cyan-400/20 shadow-2xl">

                                <template x-for="(label,key) in options">
                                    <li>
                                        <button @click="selected=key; open=false"
                                            class="w-full px-4 py-2 text-left hover:text-[#22d3ee]">
                                            <span x-text="label"></span>
                                        </button>
                                    </li>
                                </template>

                            </ul>
                        </div>

                    </div>
                </div>
            </div>


            <div class=" rounded-lg pt-1 p-1">
                <div class="h-80 w-full">
                    <canvas id="mainChart"></canvas>

                </div>
            </div>

            <?php if(!empty($allGroupCards) && count($allGroupCards) > 0): ?>
            
                <div class="mt-1">
                    <div class="flex items-center justify-between mb-1">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Accounts Summary
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    <?php echo e(count($allGroupCards)); ?> accounts
                                </span>
                            </h3>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="cardsContainer">
                        <?php $__currentLoopData = $allGroupCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $value = $card['value'];
                            ?>
                            <?php if($card['name'] == 'Capital Account'): ?>                    
                                <?php $value = $value + $Profit_Loss_Amt; ?>                        
                            <?php endif; ?>
                            <form method="GET" action="<?php echo e(route('reports.ledger')); ?>" class="card-form">
                                <button type="submit"
                                    class="group block w-full text-left focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-xl card-hover color-<?php echo e($loop->index % 6); ?>">
                                    <div
                                        class="relative  rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[92px] transition-all duration-300 hover:shadow-md">
                                        <div class="p-4 pl-6">
                                            <div class="flex items-start justify-between">
                                                <div class="pr-3 flex-1">
                                                    <div
                                                        class="text-[12px] uppercase tracking-wide text-gray-500 dark:text-gray-400 truncate">
                                                        <?php echo e($card['label']); ?>

                                                    </div>
                                                    <div class="mt-0.5 text-xl md:text-2xl font-semibold leading-tight text-gray-900 dark:text-white tabular-nums"
                                                        style="font-size: 1rem !important;">
                                                        ₹ <?php echo e($fmt(round($value))); ?>

                                                    </div>
                                                </div>

                                                <div class="shrink-0">
                                                    
                                                    
                                                    <img src="<?php echo e($card['icon']); ?>" class="h-5 w-5 md:h-10 md:w-10 object-contain"
                                                        alt="icon">
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                                <input type="hidden" name="group_id" value="<?php echo e($card['iGroupId']); ?>" />
                                <input type="hidden" name="from" value="<?php echo e($fromVal); ?>">
                                <input type="hidden" name="to" value="<?php echo e($toVal); ?>">
                            </form>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php elseif($allGroups->isNotEmpty() && !empty($selectedGroups)): ?>
                
                <?php
                    $groupsToDisplay = $selectedGroups;
                    $displayedCount = 0;
                ?>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="cardsContainer">
                    <?php $__currentLoopData = $groupsToDisplay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $groupId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        
                            $group = $allGroups->firstWhere('iGroupId', $groupId);
                            if (!$group) {
                                continue;
                            }
                            $displayedCount++;
                            $closingBalance = round($group->Closing) ?? 0;
                            $groupName = $group->strGroupName ?? 'Unknown Group';
                            $accentColor = $this->getAccentColor($groupName);
                            $groupIcon = $this->getGroupIcon($groupName);
                        ?>
                        <?php if($group->strGroupName == 'Capital Account'): ?>
                            <?php $closingBalance += $Profit_Loss_Amt; ?>
                        <?php endif; ?>
                        <form method="GET" action="<?php echo e(route('reports.ledger')); ?>" class="card-form">
                            <button type="submit"
                                class="group block w-full text-left focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-xl card-hover">
                                <div
                                    class="relative bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[92px] transition-all duration-300 hover:shadow-md">
                                    <div class="absolute inset-y-0 left-0 w-1.5 <?php echo e($leftBar($accentColor)); ?>"></div>

                                    <div class="p-4 pl-6">
                                        <div class="flex items-start justify-between">
                                            <div class="pr-3 flex-1">
                                                <div
                                                    class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400 truncate">
                                                    <?php echo e($groupName); ?>

                                                </div>
                                                <div class="mt-0.5 text-xl md:text-2xl font-semibold leading-tight text-gray-900 dark:text-white tabular-nums"
                                                    style="font-size: 1rem !important;">
                                                    ₹ <?php echo e($fmt($closingBalance)); ?>

                                                </div>
                                            </div>

                                            <div class="shrink-0">
                                                <div
                                                    class="h-9 w-9 md:h-10 md:w-10 rounded-full flex items-center justify-center <?php echo e($chip($accentColor)); ?> transition-colors group-hover:bg-opacity-80">
                                                    <i class="<?php echo e($groupIcon); ?> text-sm md:text-base"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </button>
                            <input type="hidden" name="group_id" value="<?php echo e($groupId); ?>" />
                            <input type="hidden" name="from" value="<?php echo e($fromVal); ?>">
                            <input type="hidden" name="to" value="<?php echo e($toVal); ?>">
                        </form>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <?php if($displayedCount === 0): ?>
                    <div
                        class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                            <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                            Selected groups not found in your account. Please use "Customize Groups" to select available groups.
                        </p>
                    </div>
                <?php endif; ?>
            <?php elseif($allGroups->isEmpty()): ?>
                <div
                    class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                        <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                        No groups found for your account. Please contact administrator.
                    </p>
                </div>
            <?php else: ?>
                <div
                    class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                        <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                        No groups available. Please use "Customize Groups" to select groups to display.
                    </p>
                </div>
            <?php endif; ?>
            
        </div>
    </div>

    
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        const charts = <?php echo json_encode($charts ?? [], 15, 512) ?>;
        console.log(charts);
        const chartByKey = key => charts.find(x => {
            const normalized = String(x?.key ?? '').toLowerCase();
            return normalized === String(key).toLowerCase();
        });

        const chartMap = {
            salesPurchase: chartByKey('sp') || chartByKey(1),
            creditorsDebtors: chartByKey('cd') || chartByKey(2),
            receiptPayment: chartByKey('rp') || chartByKey(3),
            cashBank: chartByKey('cb') || chartByKey(4),
            incomeExpense: chartByKey('ie') || chartByKey(5),
        };

        if (!chartMap.salesPurchase) {
            console.error('salesPurchase chart data missing');
        }

        if (!chartMap.creditorsDebtors) {
            console.error('creditorsDebtors chart data missing');
        }

        if (!chartMap.receiptPayment) {
            console.error('receiptPayment chart data missing');
        }

        if (!chartMap.cashBank) {
            console.error('cashBank chart data missing');
        }

        let activeType = <?php echo e((int) ($activeType ?? 1)); ?>;
        // let chart = null;
        let salesChart = null;
        let mainChart = null;

        let currentView = 'chart';
        // let currentMetric = 'sales';
        let currentMetric = 'Sales Accounts';
        let currentCompare = 'none';

        let isRenderingChart = false;
        let isChartInitialized = false;
        const chartMetrics = {
            'Sales Accounts': 'Sales',
            'Purchase Accounts': 'Purchase',
            'Rcpt': 'Receipt',
            'Pymt': 'Payment',
            'Cash-in-Hand': 'Cash',
            'Bank Accounts': 'Bank'
        };

        const advancedMetrics = {
            'sales_purchase': 'Sales vs Purchase',
            'receipt_payment': 'Receipt vs Payment',
            'creditors_debtors': 'Creditors vs Debtors',
            'cash_bank': 'Cash & Bank Flow'
        };

        // Format numbers with Indian formatting
        const fmt = v => new Intl.NumberFormat('en-IN', {
            maximumFractionDigits: 2
        }).format(Number(v) || 0);

        function formatCompactAmount(value) {
            const num = Number(value) || 0;
            const abs = Math.abs(num);

            if (abs >= 10000000) return `${(num / 10000000).toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1')}CR`;
            if (abs >= 100000) return `${(num / 100000).toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1')}L`;
            if (abs >= 1000) return `${(num / 1000).toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1')}K`;

            return fmt(num);
        }


        // Organize charts by type for easy access
        const byKey = {};
        charts.forEach(c => byKey[c.key] = c);

        // Calculate cumulative data
        function calculateCumulativeData(data) {
            if (!data || data.length === 0) return [];

            const cumulative = [];
            let runningTotal = 0;

            for (let i = 0; i < data.length; i++) {
                runningTotal += Number(data[i]) || 0;
                cumulative.push(runningTotal);
            }

            return cumulative;
        }

        // Reset dropdowns when any tab button is clicked
        function resetDropdowns() {
            document.getElementById('metricSelect').value = '';
            document.getElementById('compareSelect').value = 'none';
        }

        // Get appropriate labels based on active tab and metric
        function getLabels(type, metric) {
            const tabLabels = {
                1: {
                    in: 'Sales',
                    out: 'Purchase'
                },
                2: {
                    in: 'Debtors',
                    out: 'Creditors'
                },
                3: {
                    in: 'Receipt',
                    out: 'Payment'
                },
                4: {
                    in: 'Cash In',
                    out: 'Cash Out'
                }
            };

            const defaultLabels = tabLabels[type] || {
                in: 'In',
                out: 'Out'
            };

            // If specific metric is selected, override labels
            if (metric === 'Sales Accounts') return {
                in: 'Sales',
                out: ''
            };
            if (metric === 'Purchase Accounts') return {
                in: '',
                out: 'Purchase'
            };
            if (metric === 'Sundry Debtors') return {
                in: 'Debtors',
                out: ''
            };
            if (metric === 'Sundry Creditors') return {
                in: '',
                out: 'Creditors'
            };
            if (metric === 'Rcpt') return {
                in: 'Receipt',
                out: ''
            };
            if (metric === 'Pymt') return {
                in: '',
                out: 'Payment'
            };
            if (metric === 'Cash-in-Hand') return {
                
                in: 'Cash In',
                out: 'Cash Out'
            };
            
            if (metric === 'Bank Accounts') return {
                in: 'Bank In',
                out: 'Bank Out'
            };

            return defaultLabels;
        }

        // Update bottom labels dynamically
        function updateBottomLabels(labels) {
            const parentText = document.querySelector('.text-xs.text-gray-700.dark\\:text-gray-300');
            if (parentText) {
                let newText = '';

                if (labels.in) {
                    newText +=
                        `${labels.in}: <strong id="totIn" class="text-sky-700 dark:text-sky-300">0.00</strong>`;
                }

                if (labels.in && labels.out) {
                    newText += '&nbsp;|&nbsp;';
                }

                if (labels.out) {
                    newText +=
                        `${labels.out}: <strong id="totOut" class="text-fuchsia-600 dark:text-fuchsia-400">0.00</strong>`;
                }

                parentText.innerHTML = newText;
            }
        }

        // CUMULATIVE BAR CHART FUNCTION
        function renderCumulativeBarChart(c, metric) {
            metric = normalizeMetric(metric);
            const compare = document.getElementById('compareSelect')?.value || 'none';
            const datasets = [];

            // console.log('Rendering CUMULATIVE BAR CHART for metric:', metric);

            // Get the data based on selected metric
            let currentData = [];
            let cumulativeData = [];
            let labelName = '';
            let colorCurrent = '#3b82f6';
            let colorCumulative = '#1e40af';
            let prevQuarterSeries = [];
            let prevYearQuarterSeries = [];
            /*
            |--------------------------------------------------------------------------
            | LOAD CORRECT CHART OBJECT
            |--------------------------------------------------------------------------
            */

            if (
                metric === 'Sales Accounts' ||
                metric === 'Purchase Accounts'
            ) {
                c = chartMap.salesPurchase;
            }

            else if (
                metric === 'Sundry Debtors' ||
                metric === 'Sundry Creditors'
            ) {
                c = chartMap.creditorsDebtors;
            }

            else if (
                metric === 'Rcpt' ||
                metric === 'Pymt'
            ) {
                c = chartMap.receiptPayment;
            }

            else if (
                metric === 'Cash-in-Hand' ||
                metric === 'Bank Accounts'
            ) {
                c = chartMap.cashBank;
            }

            if ((metric === 'Sales Accounts' || metric === 'sales') && c.in && c.in.length > 0) {
                
                //currentData = c.in.map(v => Number(v) || 0);
                //currentData = compare === 'none' ? c.in.map(v => Number(v) || 0) : convertCumulativeToMonthly(c.in);
                currentData = c.in.map(v => Number(v) || 0);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Sales';
                colorCurrent = '#22d3ee'; // Light Blue
                colorCumulative = '#a78bfa'; // Dark Blue
            } else if ((metric === 'Purchase Accounts' || metric === 'purchase') && c.out && c.out.length > 0) {
                //currentData = c.out.map(v => Number(v) || 0);
                // currentData = compare === 'none' ? c.out.map(v => Number(v) || 0) : convertCumulativeToMonthly(c.out);
                currentData = c.out.map(v => Number(v) || 0);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Purchase';
                colorCurrent = '#22d3ee'; // Light Orange
                colorCumulative = '#34d399'; // Dark Orange
            } 
            
             else if ((metric === 'Rcpt' || metric === 'receipt') && c.in && c.in.length > 0) {
                c = getChartDataByMetric(3 ,'Rcpt');
                //currentData = c.in.map(v => Number(v) || 0);
                // currentData = compare === 'none' ? c.in.map(v => Number(v) || 0) : convertCumulativeToMonthly(c.in);
                currentData = c.in.map(v => Number(v) || 0);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Receipts';
                colorCurrent = '#22d3ee'; // Light Cyan
                colorCumulative = '#60a5fa'; // Dark Cyan
            } else if ((metric === 'Pymt' || metric === 'payment') && c.out && c.out.length > 0) {
                c = getChartDataByMetric(3 ,'Pymt');
                console.log(c.out);
                // currentData = c.out.map(v => Number(v) || 0);
                // currentData = compare === 'none' ? c.out.map(v => Number(v) || 0) : convertCumulativeToMonthly(c.out);
                currentData = c.out.map(v => Math.abs(Number(v) || 0));
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Payments';
                colorCurrent = '#22d3ee'; // Light Red
                colorCumulative = '#a78bfa'; // Dark Red
            } else if (metric === 'Cash-in-Hand' || metric === 'cash') {
                currentData = compare === 'none'
                    ? c.in.map(v => Number(v) || 0)
                    : convertCumulativeToMonthly(c.in);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Cash';
                colorCurrent = '#22d3ee';
                colorCumulative = '#34d399';
            }
            else if (metric === 'Bank Accounts' || metric === 'bank') {
                currentData = compare === 'none'
                    ? c.out.map(v => Number(v) || 0)
                    : convertCumulativeToMonthly(c.out);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Bank';
                colorCurrent = '#60a5fa';
                colorCumulative = '#a78bfa';
            }
            else if (metric === 'Sundry Debtors') {
                currentData = compare === 'none'
                    ? c.in.map(v => Number(v) || 0)
                    : convertCumulativeToMonthly(c.in);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Debtors';
                colorCurrent = '#22d3ee';
                colorCumulative = '#fbbf24';
            }
            else if (metric === 'Sundry Creditors') {
                currentData = compare === 'none'
                    ? c.out.map(v => Number(v) || 0)
                    : convertCumulativeToMonthly(c.out);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Creditors';
                colorCurrent = '#22d3ee';
                colorCumulative = '#f472b6';
            }
            if (compare === 'prev-quarter' && currentData.length > 0) {
                // const qsum = (arr, start) =>
                //     (Number(arr[start]) || 0) +
                //     (Number(arr[start + 1]) || 0) +
                //     (Number(arr[start + 2]) || 0);
                // const quarterTotals = (arr = []) => ([
                //     (Number(arr[0]) || 0) + (Number(arr[1]) || 0) + (Number(arr[2]) || 0),
                //     (Number(arr[3]) || 0) + (Number(arr[4]) || 0) + (Number(arr[5]) || 0),
                //     (Number(arr[6]) || 0) + (Number(arr[7]) || 0) + (Number(arr[8]) || 0),
                //     (Number(arr[9]) || 0) + (Number(arr[10]) || 0) + (Number(arr[11]) || 0)
                // ]);

                // const inMetricSet = ['Sales Accounts', 'Rcpt', 'Cash-in-Hand', 'Sundry Debtors', 'sales', 'receipt', 'cash'];
                // const prevYearSeries = inMetricSet.includes(metric)
                //     ? (c.prevYearIn || [])
                //     : (c.prevYearOut || []);

                // // currentData = [
                // //     qsum(currentData, 9),
                // //     qsum(currentData, 0),
                // //     qsum(prevYearSeries, 0)
                // // ];

                // const currentQuarterSeries = quarterTotals(currentData);
                // prevQuarterSeries = [
                //     currentQuarterSeries[3] || 0,
                //     currentQuarterSeries[0] || 0,
                //     currentQuarterSeries[1] || 0,
                //     currentQuarterSeries[2] || 0
                // ];
                // prevYearQuarterSeries = quarterTotals(prevYearSeries);
                // currentData = currentQuarterSeries;
                const isInMetricForQuarter = (
                    metric === 'Sales Accounts' ||
                    metric === 'Rcpt' ||
                    metric === 'Cash-in-Hand' ||
                    metric === 'Sundry Debtors'
                );

                const quarterCompare = Array.isArray(c?.quarterCompare) ? c.quarterCompare : [];
                
                if (quarterCompare.length > 0) {
                    prevQuarterSeries = quarterCompare.map(q => Number(q?.bars?.[0]?.[isInMetricForQuarter ? 'in' : 'out']) || 0);
                    currentData = quarterCompare.map(q => Number(q?.bars?.[1]?.[isInMetricForQuarter ? 'in' : 'out']) || 0);
                    prevYearQuarterSeries = quarterCompare.map(q => Number(q?.bars?.[2]?.[isInMetricForQuarter ? 'in' : 'out']) || 0);
                } else {
                    const quarterTotals = (arr = []) => ([
                        (Number(arr[0]) || 0) + (Number(arr[1]) || 0) + (Number(arr[2]) || 0),
                        (Number(arr[3]) || 0) + (Number(arr[4]) || 0) + (Number(arr[5]) || 0),
                        (Number(arr[6]) || 0) + (Number(arr[7]) || 0) + (Number(arr[8]) || 0),
                        (Number(arr[9]) || 0) + (Number(arr[10]) || 0) + (Number(arr[11]) || 0)
                    ]);

                    const prevYearSeries = isInMetricForQuarter ? (c.prevYearIn || []) : (c.prevYearOut || []);
                    const currentQuarterSeries = quarterTotals(currentData);
                    prevQuarterSeries = [
                        currentQuarterSeries[3] || 0,
                        currentQuarterSeries[0] || 0,
                        currentQuarterSeries[1] || 0,
                        currentQuarterSeries[2] || 0
                    ];
                    prevYearQuarterSeries = quarterTotals(prevYearSeries);
                    currentData = currentQuarterSeries;
                }
            }

            // If we have data, create the two bars
            if (currentData.length > 0) {
                const currentLabel = compare === 'prev-quarter'
                    ? 'Current Quarter'
                    : `${labelName} (Current)`;
                // Current Month Bar
                datasets.push({
                    // label: `${labelName} (Current)`,
                    label: currentLabel,
                    data: currentData,
                    backgroundColor: function(ctx) {
                        const chart = ctx.chart;
                        const { ctx: c, chartArea } = chart;
                        if (!chartArea) return colorCurrent;

                        const gradient = c.createLinearGradient(
                            0,
                            chartArea.top,
                            0,
                            chartArea.bottom
                        );

                        // gradient.addColorStop(0, colorCurrent + 'FF');
                        // gradient.addColorStop(0.4, colorCurrent + '99');
                        // gradient.addColorStop(1, colorCurrent + '22');
                        gradient.addColorStop(0, colorCurrent + '88');
                        gradient.addColorStop(0.3, colorCurrent + '99');
                        gradient.addColorStop(0.7, colorCurrent + '33');
                        gradient.addColorStop(1, colorCurrent + '05');


                        return gradient;
                    },

                    borderColor: colorCurrent,
                    borderWidth: 1.5,
                    borderRadius: 4,
                    borderSkipped: false,

                    grouped: true,
                    categoryPercentage: 0.6,
                    barPercentage: 0.8,
                    maxBarThickness: 40
                });

                /*
                |--------------------------------------------------------------------------
                | NO COMPARE = SHOW CUMULATIVE
                |--------------------------------------------------------------------------
                */

                if (compare === 'none') {

                    datasets.push({
                        label: `${labelName} (Cumulative)`,
                        data: cumulativeData,
                        backgroundColor: function(ctx) {

                            const chart = ctx.chart;
                            const { ctx: c, chartArea } = chart;

                            if (!chartArea) return colorCumulative;

                            const gradient = c.createLinearGradient(
                                0,
                                chartArea.top,
                                0,
                                chartArea.bottom
                            );

                            // gradient.addColorStop(0, colorCumulative + 'FF');
                            gradient.addColorStop(0, colorCumulative + '88');
                            gradient.addColorStop(0.3, colorCumulative + '99');
                            gradient.addColorStop(0.7, colorCumulative + '33');
                            gradient.addColorStop(1, colorCumulative + '05');

                            return gradient;
                        },

                        borderColor: colorCumulative,
                        borderWidth: 1.5,
                        borderRadius: 4,
                        borderSkipped: false,
                        plugins: [glowPlugin],
                        categoryPercentage: 0.6,
                        barPercentage: 0.8,
                        maxBarThickness: 40
                    });
                }

                /*
                |--------------------------------------------------------------------------
                | COMPARE MODE
                |--------------------------------------------------------------------------
                */

                else {

                    let compareData = [];
                    let compareLabel = '';

                    const isInMetric =
                        metric === 'Sales Accounts' ||
                        metric === 'Rcpt' ||
                        metric === 'Cash-in-Hand' ||
                        metric === 'Sundry Debtors';
                    const isOutMetric =
                        metric === 'Purchase Accounts' ||
                        metric === 'Pymt' ||
                        metric === 'Bank Accounts' ||
                        metric === 'Sundry Creditors';
                    if (compare === 'prev-year') {

                        compareData = isInMetric
                            ? (c.prevYearIn || [])
                            : (c.prevYearOut || []);

                        compareLabel = 'Previous Year';
                    }

                    else if (compare === 'prev-month') {

                        compareData = isInMetric
                            ? (c.prevMonthIn || [])
                            : (c.prevMonthOut || []);

                        compareLabel = 'Previous Month';
                    }

                    else if (compare === 'prev-quarter') {

                        // compareData = isInMetric
                        //     ? (c.prevQuarterIn || [])
                        //     : (c.prevQuarterOut || []);

                        // const sourceSeries = isInMetric
                        //     ? (c.in || [])
                        //     : (c.out || []);
                        // const prevYearSeries = isInMetric
                        //     ? (c.prevYearIn || [])
                        //     : (c.prevYearOut || []);

                        // // compareLabel = 'Previous Quarter';
                        // const qsum = (arr, start) =>
                        //     (Number(arr[start]) || 0) +
                        //     (Number(arr[start + 1]) || 0) +
                        //     (Number(arr[start + 2]) || 0);

                        // currentData = [
                        //     qsum(sourceSeries, 9),
                        //     qsum(sourceSeries, 0),
                        //     qsum(prevYearSeries, 0)
                        // ];

                        // compareData = [];
                        // compareLabel = 'Quarter Set';
                        compareData = prevQuarterSeries;
                        compareLabel = 'Previous Quarter';
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | IMPORTANT FIX
                    |--------------------------------------------------------------------------
                    | DO NOT convert compare data again
                    | because backend already sends monthly values
                    |--------------------------------------------------------------------------
                    */

                    compareData = compareData.map(v => Number(v) || 0);
                    if (compareData.length > 0) {
                        datasets.push({
                            // label: `${labelName} (${compareLabel})`,
                            // data: compareData,
                            label: `${labelName} (${compareLabel})`,
                            data: compareData,

                            backgroundColor: function(ctx) {

                                const chart = ctx.chart;
                                const { ctx: c, chartArea } = chart;

                                if (!chartArea) return colorCumulative;

                                const gradient = c.createLinearGradient(
                                    0,
                                    chartArea.top,
                                    0,
                                    chartArea.bottom
                                );

                                // gradient.addColorStop(0, colorCumulative + 'FF');
                                gradient.addColorStop(0, colorCumulative + '88');
                                gradient.addColorStop(0.3, colorCumulative + '99');
                                gradient.addColorStop(0.7, colorCumulative + '33');
                                gradient.addColorStop(1, colorCumulative + '05');

                                return gradient;
                            },

                            borderColor: colorCumulative,
                            borderWidth: 1.5,
                            borderRadius: 4,
                            borderSkipped: false,
                            plugins: [glowPlugin],
                            options: {
                                scales: {
                                    x: {
                                        stacked: false
                                    },
                                    y: {
                                        stacked: false
                                    }
                                },
                            },
                            grouped: true,
                            order: 2,
                            categoryPercentage: 0.6,
                            barPercentage: 0.8,
                            maxBarThickness: 40
                        });
                    }        
                }

                if (compare === 'prev-quarter' && prevYearQuarterSeries.length > 0) {
                    datasets.push({
                        label: 'Previous Year',
                        data: prevYearQuarterSeries.map(v => Number(v) || 0),
                        backgroundColor: '#fbbf2466',
                        borderColor: '#fbbf24',
                        borderWidth: 1.5,
                        borderRadius: 4,
                        borderSkipped: false,
                        grouped: true,
                        order: 3,
                        categoryPercentage: 0.6,
                        barPercentage: 0.8,
                        maxBarThickness: 40
                    });
                }

                // Cumulative Bar
                // datasets.push({
                //     label: `${labelName} (Cumulative)`,
                //     data: cumulativeData,
                //     backgroundColor: function(ctx) {
                //         const chart = ctx.chart;
                //         const {
                //             ctx: c,
                //             chartArea
                //         } = chart;
                //         if (!chartArea) return colorCumulative;

                //         const gradient = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);

                //         gradient.addColorStop(0, colorCumulative + 'FF'); // strong top
                //         gradient.addColorStop(0.3, colorCumulative + '99');
                //         gradient.addColorStop(0.7, colorCumulative + '33');
                //         gradient.addColorStop(1, colorCumulative + '05'); // soft fade

                //         return gradient;
                //     },
                //     borderColor: colorCumulative,
                //     borderWidth: 1.5,
                //     borderRadius: 4,
                //     plugins: [glowPlugin],
                //     categoryPercentage: 0.6,
                //     borderSkipped: false,
                //     barPercentage: 0.8
                // });
            }

            return datasets;
        }

        function normalizeMetric(metric) {

            const map = {

                sales: 'Sales Accounts',
                purchase: 'Purchase Accounts',
                receipt: 'Rcpt',
                payment: 'Pymt',
                cash: 'Cash-in-Hand',
                bank: 'Bank Accounts',
                debtor: 'Sundry Debtors',
                debtors: 'Sundry Debtors',

                creditor: 'Sundry Creditors',
                creditors: 'Sundry Creditors',

                sales_purchase: 'sales_purchase',
                receipt_payment: 'receipt_payment',
                creditors_debtors: 'creditors_debtors',
                cash_bank: 'cash_bank'
            };

            return map[metric] || metric;
        }

        function getChartDataByMetric(type, metric) {
    const metricToTypeMap = {
        sales_purchase: 1,
        creditors_debtors: 2,
        receipt_payment: 3,
        cash_bank: 4
    };

    const resolvedType = metricToTypeMap[metric] || Number(type);

    switch (resolvedType) {
        case 1: return chartMap.salesPurchase;
        case 2: return chartMap.creditorsDebtors;
        case 3: return chartMap.receiptPayment;
        case 4: return chartMap.cashBank;
        case 5: return chartMap.incomeExpense;
        default: return null;
    }
}

function renderLineChart(ctx, c, labels, metric, compare) {
    metric = normalizeMetric(metric);
    let data1 = [];
    let data2 = [];

    let label1 = '';
    let label2 = '';

    let compareIn = [];
    let compareOut = [];
    let compare2In = [];
    let compare2Out = [];
    const numberValue = value => Number(value) || 0;
    const quarterTotals = (arr = []) => [
        numberValue(arr[0]) + numberValue(arr[1]) + numberValue(arr[2]),
        numberValue(arr[3]) + numberValue(arr[4]) + numberValue(arr[5]),
        numberValue(arr[6]) + numberValue(arr[7]) + numberValue(arr[8]),
        numberValue(arr[9]) + numberValue(arr[10]) + numberValue(arr[11]),
    ];
    const quarterCompareSeries = () => {
        const quarterCompare = Array.isArray(c?.quarterCompare) ? c.quarterCompare : [];
        if (!quarterCompare.length) return null;

        const valueFromBar = (quarter, barIndex, key) => numberValue(quarter?.bars?.[barIndex]?.[key]);

        return {
            labels: quarterCompare.map(q => q?.label || ''),
            currentIn: quarterCompare.map(q => valueFromBar(q, 1, 'in')),
            currentOut: quarterCompare.map(q => valueFromBar(q, 1, 'out')),
            previousQuarterIn: quarterCompare.map(q => valueFromBar(q, 0, 'in')),
            previousQuarterOut: quarterCompare.map(q => valueFromBar(q, 0, 'out')),
            previousYearIn: quarterCompare.map(q => valueFromBar(q, 2, 'in')),
            previousYearOut: quarterCompare.map(q => valueFromBar(q, 2, 'out')),
        };
    };
    // switch (metric) {

    //     case 'sales_purchase':
    //         if (compare === 'prev-quarter') {

    //             /*
    //             |--------------------------------------------------------------------------
    //             | ONLY 3 BARS
    //             |--------------------------------------------------------------------------
    //             */

    //             //labels = ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar'];
    //             // const makeQuarter = (arr = []) => [
    //             //     (arr[0] || 0) + (arr[1] || 0) + (arr[2] || 0),
    //             //     (arr[3] || 0) + (arr[4] || 0) + (arr[5] || 0),
    //             //     (arr[6] || 0) + (arr[7] || 0) + (arr[8] || 0),
    //             //     (arr[9] || 0) + (arr[10] || 0) + (arr[11] || 0),
    //             // ];
    //             const quarterSeries = quarterCompareSeries();
    //             labels = quarterSeries
    //                 ? (Array.isArray(labels) && labels.length === quarterSeries.labels.length ? labels : quarterSeries.labels)
    //                 : ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar'];
    //             /*
    //             |--------------------------------------------------------------------------
    //             | CURRENT QUARTER
    //             | Apr-Jun 2026
    //             |--------------------------------------------------------------------------
    //             */

    //             // const currentQuarterIn =
    //             //     (c.in?.[0] || 0) +
    //             //     (c.in?.[1] || 0) +
    //             //     (c.in?.[2] || 0);

    //             // const currentQuarterOut =
    //             //     (c.out?.[0] || 0) +
    //             //     (c.out?.[1] || 0) +
    //             //     (c.out?.[2] || 0);

    //             /*
    //             |--------------------------------------------------------------------------
    //             | PREVIOUS QUARTER
    //             | Jan-Mar 2026
    //             |--------------------------------------------------------------------------
    //             */

    //             // const previousQuarterIn =
    //             //     (c.in?.[9] || 0) +
    //             //     (c.in?.[10] || 0) +
    //             //     (c.in?.[11] || 0);

    //             // const previousQuarterOut =
    //             //     (c.out?.[9] || 0) +
    //             //     (c.out?.[10] || 0) +
    //             //     (c.out?.[11] || 0);

    //             /*
    //             |--------------------------------------------------------------------------
    //             | PREVIOUS YEAR SAME QUARTER
    //             | Apr-Jun 2025
    //             |--------------------------------------------------------------------------
    //             */

    //             // const previousYearIn =
    //             //     (c.prevYearIn?.[0] || 0) +
    //             //     (c.prevYearIn?.[1] || 0) +
    //             //     (c.prevYearIn?.[2] || 0);

    //             // const previousYearOut =
    //             //     (c.prevYearOut?.[0] || 0) +
    //             //     (c.prevYearOut?.[1] || 0) +
    //             //     (c.prevYearOut?.[2] || 0);

    //             /*
    //             |--------------------------------------------------------------------------
    //             | FINAL DATA
    //             |--------------------------------------------------------------------------
    //             */

    //             // data1 = [
    //             //     previousQuarterIn,
    //             //     currentQuarterIn,
    //             //     previousYearIn
    //             // ];

    //             // data2 = [
    //             //     previousQuarterOut,
    //             //     currentQuarterOut,
    //             //     previousYearOut
    //             // ];

    //             // compareIn = [];
    //             // compareOut = [];
    //             // data1 = makeQuarter(c.in || []);
    //             // data2 = makeQuarter(c.out || []);
    //             // compareIn = makeQuarter(c.prevMonthIn || []);
    //             // compareOut = makeQuarter(c.prevMonthOut || []);
    //             // compare2In = makeQuarter(c.prevYearIn || []);
    //             // compare2Out = makeQuarter(c.prevYearOut || []);
    //             data1 = quarterSeries?.currentIn || quarterTotals(c.in || []);
    //             data2 = quarterSeries?.currentOut || quarterTotals(c.out || []);
    //             compareIn = quarterSeries?.previousQuarterIn || quarterTotals(c.prevQuarterIn || []);
    //             compareOut = quarterSeries?.previousQuarterOut || quarterTotals(c.prevQuarterOut || []);
    //             compare2In = quarterSeries?.previousYearIn || quarterTotals(c.prevYearIn || []);
    //             compare2Out = quarterSeries?.previousYearOut || quarterTotals(c.prevYearOut || []);
    //         } else {
    //             data1 = c.in || [];
    //             data2 = c.out || [];
    //             if (compare === 'prev-month') {
    //                 compareIn  = c.prevMonthIn || [];
    //                 compareOut = c.prevMonthOut || [];
    //             } else if (compare === 'prev-year') {
    //                 compareIn  = c.prevYearIn || [];
    //                 compareOut = c.prevYearOut || [];
    //             }
    //         }
    //         // data1 = c.in || [];
    //         // data2 = c.out || [];
    //         label1 = 'Sales';
    //         label2 = 'Purchase';
    //         break;

    //     case 'receipt_payment':
    //         if (compare === 'prev-quarter') {

    //             // labels = ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar'];
    //             // const makeQuarter = (arr = []) => [
    //             //     (arr[0] || 0) + (arr[1] || 0) + (arr[2] || 0),
    //             //     (arr[3] || 0) + (arr[4] || 0) + (arr[5] || 0),
    //             //     (arr[6] || 0) + (arr[7] || 0) + (arr[8] || 0),
    //             //     (arr[9] || 0) + (arr[10] || 0) + (arr[11] || 0),
    //             // ];
    //             const quarterSeries = quarterCompareSeries();
    //             labels = quarterSeries
    //                 ? (Array.isArray(labels) && labels.length === quarterSeries.labels.length ? labels : quarterSeries.labels)
    //                 : ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar'];

    //             // compareIn = [];
    //             // compareOut = [];
    //             // data1 = makeQuarter(c.in || []);
    //             // data2 = makeQuarter(c.out || []);
    //             // compareIn = makeQuarter(c.prevMonthIn || []);
    //             // compareOut = makeQuarter(c.prevMonthOut || []);
    //             // compare2In = makeQuarter(c.prevYearIn || []);
    //             // compare2Out = makeQuarter(c.prevYearOut || []);
    //              data1 = quarterSeries?.currentIn || quarterTotals(c.in || []);
    //             data2 = quarterSeries?.currentOut || quarterTotals(c.out || []);
    //             compareIn = quarterSeries?.previousQuarterIn || quarterTotals(c.prevQuarterIn || []);
    //             compareOut = quarterSeries?.previousQuarterOut || quarterTotals(c.prevQuarterOut || []);
    //             compare2In = quarterSeries?.previousYearIn || quarterTotals(c.prevYearIn || []);
    //             compare2Out = quarterSeries?.previousYearOut || quarterTotals(c.prevYearOut || []);
    //         } else {
    //             data1 = c.in || [];
    //             data2 = c.out || [];
    //             if (compare === 'prev-month') {
    //                 compareIn  = c.prevMonthIn || [];
    //                 compareOut = c.prevMonthOut || [];
    //             } else if (compare === 'prev-year') {
    //                 compareIn  = c.prevYearIn || [];
    //                 compareOut = c.prevYearOut || [];
    //             }
    //         }
    //         // data1 = c.in || [];
    //         // data2 = c.out || [];
    //         label1 = 'Receipt';
    //         label2 = 'Payment';
    //         break;

    //     case 'creditors_debtors':
    //         if (compare === 'prev-quarter') {
    //             labels = ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar']
    //             const makeQuarter = (arr = []) => {
    //                 return [
    //                     (arr[0] || 0) + (arr[1] || 0) + (arr[2] || 0),
    //                     (arr[3] || 0) + (arr[4] || 0) + (arr[5] || 0),
    //                     (arr[6] || 0) + (arr[7] || 0) + (arr[8] || 0),
    //                     (arr[9] || 0) + (arr[10] || 0) + (arr[11] || 0),
    //                 ];
    //             };

    //             data1 = makeQuarter(c.in || []);
    //             data2 = makeQuarter(c.out || []);

    //             compareIn = makeQuarter(c.prevMonthIn || []);
    //             compareOut = makeQuarter(c.prevMonthOut || []);
    //         } else {
    //             data1 = c.in || [];
    //             data2 = c.out || [];
    //             if (compare === 'prev-month') {
    //                 compareIn  = c.prevMonthIn || [];
    //                 compareOut = c.prevMonthOut || [];
    //             } else if (compare === 'prev-year') {
    //                 compareIn  = c.prevYearIn || [];
    //                 compareOut = c.prevYearOut || [];
    //             }
    //         }
    //         // data1 = c.in || [];
    //         // data2 = c.out || [];
    //         label1 = 'Debtors';
    //         label2 = 'Creditors';
    //         break;

    //     case 'cash_bank':
    //         if (compare === 'prev-quarter') {
    //             labels = ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar']
    //             const makeQuarter = (arr = []) => {
    //                 return [
    //                     (arr[0] || 0) + (arr[1] || 0) + (arr[2] || 0),
    //                     (arr[3] || 0) + (arr[4] || 0) + (arr[5] || 0),
    //                     (arr[6] || 0) + (arr[7] || 0) + (arr[8] || 0),
    //                     (arr[9] || 0) + (arr[10] || 0) + (arr[11] || 0),
    //                 ];
    //             };

    //             data1 = makeQuarter(c.in || []);
    //             data2 = makeQuarter(c.out || []);

    //             compareIn = makeQuarter(c.prevMonthIn || []);
    //             compareOut = makeQuarter(c.prevMonthOut || []);
    //         } else {
    //             data1 = c.in || [];
    //             data2 = c.out || [];
    //             if (compare === 'prev-month') {
    //                 compareIn  = c.prevMonthIn || [];
    //                 compareOut = c.prevMonthOut || [];
    //             } else if (compare === 'prev-year') {
    //                 compareIn  = c.prevYearIn || [];
    //                 compareOut = c.prevYearOut || [];
    //             }
    //         }
    //         // data1 = c.in || [];
    //         // data2 = c.out || [];
    //         label1 = 'Cash';
    //         label2 = 'Bank';
    //         break;

    //     default:
    //         if (compare === 'prev-quarter') {
    //             labels = ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar']
    //             const makeQuarter = (arr = []) => {
    //                 return [
    //                     (arr[0] || 0) + (arr[1] || 0) + (arr[2] || 0),
    //                     (arr[3] || 0) + (arr[4] || 0) + (arr[5] || 0),
    //                     (arr[6] || 0) + (arr[7] || 0) + (arr[8] || 0),
    //                     (arr[9] || 0) + (arr[10] || 0) + (arr[11] || 0),
    //                 ];
    //             };

    //             data1 = makeQuarter(c.in || []);
    //             data2 = makeQuarter(c.out || []);

    //             compareIn = makeQuarter(c.prevMonthIn || []);
    //             compareOut = makeQuarter(c.prevMonthOut || []);
    //         } else {
    //             data1 = c.in || [];
    //             data2 = c.out || [];
    //             if (compare === 'prev-month') {
    //                 compareIn  = c.prevMonthIn || [];
    //                 compareOut = c.prevMonthOut || [];
    //             } else if (compare === 'prev-year') {
    //                 compareIn  = c.prevYearIn || [];
    //                 compareOut = c.prevYearOut || [];
    //             }
    //         }
    //         // data1 = c.in || [];
    //         // data2 = c.out || [];
    //         label1 = 'Inflow';
    //         label2 = 'Outflow';
    //         break;
    // }
    const makeQuarter = (arr = []) => [
        (Number(arr[0]) || 0) + (Number(arr[1]) || 0) + (Number(arr[2]) || 0),
        (Number(arr[3]) || 0) + (Number(arr[4]) || 0) + (Number(arr[5]) || 0),
        (Number(arr[6]) || 0) + (Number(arr[7]) || 0) + (Number(arr[8]) || 0),
        (Number(arr[9]) || 0) + (Number(arr[10]) || 0) + (Number(arr[11]) || 0),
    ];

    const metricLabelMap = {
        sales_purchase: ['Sales', 'Purchase'],
        receipt_payment: ['Receipt', 'Payment'],
        creditors_debtors: ['Debtors', 'Creditors'],
        cash_bank: ['Cash', 'Bank'],
    };
    [label1, label2] = metricLabelMap[metric] || ['Inflow', 'Outflow'];
    const applyQuarterCompareData = () => {
        const quarterCompare = Array.isArray(c?.quarterCompare) ? c.quarterCompare : [];

        if (compare !== 'prev-quarter' || quarterCompare.length === 0) {
            return false;
        }
        labels = quarterCompare.map(q => q?.label || '');
        compareIn = quarterCompare.map(q => Number(q?.bars?.[0]?.in) || 0);
        compareOut = quarterCompare.map(q => Number(q?.bars?.[0]?.out) || 0);
        data1 = quarterCompare.map(q => Number(q?.bars?.[1]?.in) || 0);
        data2 = quarterCompare.map(q => Number(q?.bars?.[1]?.out) || 0);
        compare2In = quarterCompare.map(q => Number(q?.bars?.[2]?.in) || 0);
        compare2Out = quarterCompare.map(q => Number(q?.bars?.[2]?.out) || 0);
        return true;
    };

    if (!applyQuarterCompareData()) {
        if (compare === 'prev-quarter') {
            labels = c.quarterLabels || c.totalsPrev?.quarterLabels || ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar'];
            data1 = makeQuarter(c.in || []);
            data2 = makeQuarter(c.out || []);
            compareIn = makeQuarter(c.prevQuarterIn || c.prevMonthIn || []);
            compareOut = makeQuarter(c.prevQuarterOut || c.prevMonthOut || []);
            compare2In = makeQuarter(c.prevYearIn || []);
            compare2Out = makeQuarter(c.prevYearOut || []);
        } else {
            data1 = c.in || [];
            data2 = c.out || [];

            if (compare === 'prev-month') {
                compareIn = c.prevMonthIn || [];
                compareOut = c.prevMonthOut || [];
            } else if (compare === 'prev-year') {
                compareIn = c.prevYearIn || [];
                compareOut = c.prevYearOut || [];
            }
        }
    }
    // if (compare === 'prev-month') {
    //     compareIn  = c.prevMonthIn || [];
    //     compareOut = c.prevMonthOut || [];
    // } else if (compare === 'prev-quarter') {
    //     compareIn  = c.prevQuarterIn || [];
    //     compareOut = c.prevQuarterOut || [];
    // } else if (compare === 'prev-year') {
    //     compareIn  = c.prevYearIn || [];
    //     compareOut = c.prevYearOut || [];
    // }
    
    const datasets = [
        {
            label: label1,
            data: data1,
            borderColor: '#22d3ee',
            backgroundColor: 'rgba(34, 211, 238, 0.18)',
            fill: true,
            tension: 0.45,
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 6
        },
        {
            label: label2,
            data: data2,
            borderColor: '#8b5cf6',
            backgroundColor: 'rgba(139, 92, 246, 0.18)',
            fill: true,
            tension: 0.45,
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 6
        }
    ];
    /*
    |--------------------------------------------------------------------------
    | COMPARE MODE
    |--------------------------------------------------------------------------
    */
    if (compare !== 'none') {
        // const ctx = document.getElementById('mainChart').getContext('2d');
        const gradientPrevIn = ctx.createLinearGradient(0, 0, 0, 400);
        // gradientPrevIn.addColorStop(0, 'rgba(52,211,153,0.30)');
        // gradientPrevIn.addColorStop(1, 'rgba(52,211,153,0.02)');
        if (compare === 'prev-year') {
            gradientPrevIn.addColorStop(0, 'rgba(236,72,153,0.42)');
            gradientPrevIn.addColorStop(1, 'rgba(236,72,153,0.08)');
        } else {
            gradientPrevIn.addColorStop(0, 'rgba(16,185,129,0.34)');
            gradientPrevIn.addColorStop(1, 'rgba(16,185,129,0.05)');
        }
        const gradientPrevOut = ctx.createLinearGradient(0, 0, 0, 400);
        // gradientPrevOut.addColorStop(0, 'rgba(251,146,60,0.30)');
        // gradientPrevOut.addColorStop(1, 'rgba(251,146,60,0.02)');
        if (compare === 'prev-year') {
            gradientPrevOut.addColorStop(0, 'rgba(59,130,246,0.42)');
            gradientPrevOut.addColorStop(1, 'rgba(59,130,246,0.08)');
        } else {
            gradientPrevOut.addColorStop(0, 'rgba(249,115,22,0.34)');
            gradientPrevOut.addColorStop(1, 'rgba(249,115,22,0.05)');
        }
        const compareLabelSuffix = compare === 'prev-year'
            ? 'Previous Year'
            : (compare === 'prev-month' ? 'Previous Month' : 'Previous Quarter');
        const compareInColor = compare === 'prev-year' ? '#ec4899' : '#10b981';
        const compareOutColor = compare === 'prev-year' ? '#3b82f6' : '#f97316';
        datasets.push({
            // label: label1 + ' Previous',
            label: label1 + ' ' + compareLabelSuffix,
            data: compareIn,
            //borderColor: '#34d399',
            //borderColor: '#10b981',
            borderColor: compareInColor,
            backgroundColor: gradientPrevIn,
            //borderDash: [5, 5],
            borderDash: compare === 'prev-year' ? [2, 4] : [5, 5],
            tension: 0.45,
            // borderWidth: 2,
            // pointRadius: 3,
            borderWidth: compare === 'prev-year' ? 3 : 2,
            pointRadius: compare === 'prev-year' ? 4 : 3,
            pointHoverRadius: 6,
            pointBorderWidth: compare === 'prev-year' ? 2 : 1,
            fill: true
        });
        datasets.push({
            label: label2 + ' ' + compareLabelSuffix,
            data: compareOut,
            //borderColor: '#fb923c',
            // borderColor: '#f97316',
             borderColor: compareOutColor,
            backgroundColor: gradientPrevOut,
            borderDash: compare === 'prev-year' ? [2, 4] : [5, 5],
            tension: 0.45,
            borderWidth: 2,
            pointRadius: 3,
            borderWidth: compare === 'prev-year' ? 3 : 2,
            pointRadius: compare === 'prev-year' ? 4 : 3,
            pointHoverRadius: 6,
            pointBorderWidth: compare === 'prev-year' ? 2 : 1,
            fill: true
        });
    }

    //  if (compare === 'prev-quarter' && compare2In.length) {
    if (compare === 'prev-quarter' && (compare2In.length || compare2Out.length)) {
        const gradientPrevYearIn = ctx.createLinearGradient(0, 0, 0, 400);
        // gradientPrevYearIn.addColorStop(0, 'rgba(250,204,21,0.22)');
        // gradientPrevYearIn.addColorStop(1, 'rgba(250,204,21,0.02)');
        gradientPrevYearIn.addColorStop(0, 'rgba(236,72,153,0.30)');
        gradientPrevYearIn.addColorStop(1, 'rgba(236,72,153,0.06)');
        const gradientPrevYearOut = ctx.createLinearGradient(0, 0, 0, 400);
        // gradientPrevYearOut.addColorStop(0, 'rgba(253,224,71,0.20)');
        // gradientPrevYearOut.addColorStop(1, 'rgba(253,224,71,0.02)');
        gradientPrevYearOut.addColorStop(0, 'rgba(59,130,246,0.30)');
        gradientPrevYearOut.addColorStop(1, 'rgba(59,130,246,0.06)');
        datasets.push({
            label: label1 + ' Previous Year',
            data: compare2In,
            borderColor: '#ec4899',
            backgroundColor: gradientPrevYearIn,
            fill: true,
            borderDash: [2, 4],
            tension: 0.45,
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBorderWidth: 2,
            pointBackgroundColor: '#0b0f19'
        }, {
            label: label2 + ' Previous Year',
            data: compare2Out,
            //borderColor: '#fde047',
            borderColor: '#3b82f6',
            // backgroundColor: '#fde04740',
            // fill: false,
            backgroundColor: gradientPrevYearOut,
            fill: true,
            borderDash: [2, 4],
            tension: 0.45,
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBorderWidth: 2,
            pointBackgroundColor: '#0b0f19'
        });
    }

    console.log('LINE DATASETS:', datasets);
    mainChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets
        },
        options: getChartOptions()
    });
}


        function shouldShowCumulativeBar(metric, compareWith) {
            const view = document.getElementById('viewType')?.value;
            // 🚫 NO cumulative in simple chart
            if(view === 'chart') return false;
            // ✅ only advance chart
            return metric && compareWith === 'none';
        }

        function formatYAxis(value) {
            // value = Number(value);

            // if (value >= 10000000) return (value / 10000000) + 'Cr';
            // if (value >= 100000) return (value / 100000) + 'L';
            // if (value >= 1000) return (value / 1000) + 'K';

            // return value;
            return formatCompactAmount(value);
        }


function renderChartFor(type, metric, compare = 'none') {
    console.log(type, metric);
    if (isRenderingChart) return;

    isRenderingChart = true;
    try {
        currentMetric = metric;
        currentCompare = compare;
        console.log('Rendering:', {
            type,
            metric,
            compare
        });
        const canvas = document.getElementById('mainChart');
        if (!canvas) {
            console.error('mainChart canvas missing');
            return;
        }
        destroyMainChart();
        const ctx = canvas.getContext('2d');
        // switch (Number(type)) {

        //     case 1:
        //         c = chartMap.salesPurchase;
        //         break;

        //     case 2:
        //         c = chartMap.creditorsDebtors;
        //         break;

        //     case 3:
        //         c = chartMap.receiptPayment;
        //         break;

        //     case 4:
        //         c = chartMap.cashBank;
        //         break;
        // }

        let c = getChartDataByMetric(type, metric);

        if (!c) {
            console.error('Chart data missing');
            return;
        }

        // const labels = c.months || [];
        const normalizedMetric = normalizeMetric(metric);
       const quarterCompareBarMetrics = [
            'Cash-in-Hand',
            'Bank Accounts',
            'Sundry Debtors',
            'Sundry Creditors'
        ];
        const shouldUseQuarterCompareBars = compare === 'prev-quarter'
            && quarterCompareBarMetrics.includes(normalizedMetric)
            && Array.isArray(c?.quarterCompare)
            && c.quarterCompare.length > 0;
        let labels = compare === 'prev-quarter'
            ? (shouldUseQuarterCompareBars
            ? c.quarterCompare.map(q => q?.label || '')
            : (c.quarterLabels || c.totalsPrev?.quarterLabels || ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar']))
            : (c.months || []);
        
        const addYearToMainChartLabel = (label, fallbackYear = '') => {
            const text = String(label || '').trim();
            if (!text) return text;
            if (/\b(19|20)\d{2}\b/.test(text)) return text;
            if (/\b'\d{2}\b/.test(text)) return text;
            if (/\d{2}$/.test(text)) return text;
            return fallbackYear ? `${text} ${fallbackYear}` : text;
        };

        const resolveLabelYears = (series = []) => {
            const monthsOrder = ['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'];
            let startYear = null;
            for (const raw of series) {
                const token = String(raw || '').toLowerCase();
                if (monthsOrder.some(m => token.includes(m))) {
                    const yyMatch = token.match(/(?:^|[^0-9])(\d{2})(?:[^0-9]|$)/);
                    const yyyyMatch = token.match(/\b(19|20)\d{2}\b/);
                    if (yyyyMatch) {
                        startYear = Number(yyyyMatch[0]);
                    } else if (yyMatch) {
                        startYear = 2000 + Number(yyMatch[1]);
                    }
                    break;
                }
            }
            if (!startYear) {
                startYear = new Date().getFullYear();
            }
            return [startYear, startYear, startYear, startYear + 1];
        };

        if (compare === 'prev-quarter' && Array.isArray(labels) && labels.length === 4) {
            const quarterCompare = Array.isArray(c?.quarterCompare) ? c.quarterCompare : [];
            // console.log('Quarter Compare Data:', quarterCompare[0]?.bars, quarterCompare[0]?.labels);
            const [y1, y2, y3, y4] = resolveLabelYears(c.months || []);
            labels = [
                addYearToMainChartLabel(labels[0], y1),
                addYearToMainChartLabel(labels[1], y2),
                addYearToMainChartLabel(labels[2], y3),
                addYearToMainChartLabel(labels[3], y4)
            ];
        } else if (Array.isArray(labels)) {
            labels = labels.map(l => addYearToMainChartLabel(l));
        }

        /*
        |--------------------------------------------------------------------------
        | CHART VIEW
        |--------------------------------------------------------------------------
        */

        // if (currentView === 'chart') {
        //     const datasets = renderCumulativeBarChart(c, metric);
        //     mainChart = new Chart(ctx, {
        //         type: 'bar',
        //         data: {
        //             labels,
        //             datasets
        //         },
        //         options: getChartOptions()
        //     });
        //     return;
        // }

        if (currentView === 'chart') {
            
            metric = normalizeMetric(metric);
             /*
            |--------------------------------------------------------------------------
            | NORMAL BAR CHARTS
            |--------------------------------------------------------------------------
            */
            const normalBarMetrics = [
                'Cash-in-Hand',
                'Bank Accounts',
                'Sundry Debtors',
                'Sundry Creditors'
            ];
            /*
            |--------------------------------------------------------------------------
            | CUMULATIVE BAR CHARTS
            |--------------------------------------------------------------------------
            */
            if (!normalBarMetrics.includes(metric)) {
                const datasets = renderCumulativeBarChart(c, metric);
                mainChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets
                    },
                    options: getChartOptions()
                });
                return;
            }

            let datasets = [];
            if (metric === 'Sundry Debtors' || metric === 'Sundry Creditors') {
                activeType = 2;
            }
            /*
            |--------------------------------------------------------------------------
            | SIMPLE BAR CHARTS
            |--------------------------------------------------------------------------
            */
            if (metric === 'Cash-in-Hand') {
                c = getChartDataByMetric(4, 'Cash-in-Hand');
                datasets.push({
                    label: 'Cash',
                    data: c.in || [],
                    backgroundColor: function(ctx) {
                        const chart = ctx.chart;
                        const { ctx:c, chartArea } = chart;
                        if (!chartArea) return '#22d3ee';
                        const gradient = c.createLinearGradient(
                            0,
                            chartArea.top,
                            0,
                            chartArea.bottom
                        );
                        gradient.addColorStop(0, '#22d3eeFF');
                        gradient.addColorStop(0.4, '#22d3ee99');
                        gradient.addColorStop(1, '#22d3ee10');
                        return gradient;
                    },
                    // borderColor: '#22d3ee',
                    borderColor: '#06b6d4',
                    borderWidth: 1.5,
                    borderRadius: 4,
                    borderSkipped: false,
                    grouped: true,
                    categoryPercentage: 0.6,
                    barPercentage: 0.8,
                    maxBarThickness: 40
                });
            } else if (metric === 'Bank Accounts') {
                //c = getChartDataByMetric(4 ,'Cash-in-Hand');
                c = getChartDataByMetric(4, 'Bank Accounts');
                console.log(c);
                datasets.push({
                    label: 'Bank',
                    data: c.out || [],
                    backgroundColor: function(ctx) {

                        const chart = ctx.chart;
                        const { ctx:c, chartArea } = chart;
                        if (!chartArea) return '#fbbf24';
                        const gradient = c.createLinearGradient(
                            0,
                            chartArea.top,
                            0,
                            chartArea.bottom
                        );
                        gradient.addColorStop(0, '#fbbf24FF');
                        gradient.addColorStop(0.4, '#fbbf2499');
                        gradient.addColorStop(1, '#fbbf2410');
                        return gradient;
                    },
                    borderColor: '#fbbf24',
                    borderWidth: 1.5,
                    borderRadius: 4,
                    borderSkipped: false,
                    grouped: true,
                    categoryPercentage: 0.6,
                    barPercentage: 0.8,
                    maxBarThickness: 40
                });
            } else if (metric === 'Sundry Debtors') {
                c = getChartDataByMetric(2 ,'Sundry Debtors');
                datasets.push({
                    label: 'Debtors',
                    data: c.in || [],
                    backgroundColor: function(ctx) {
                        const chart = ctx.chart;
                        const { ctx:c, chartArea } = chart;
                        if (!chartArea) return '#34d399';
                        const gradient = c.createLinearGradient(
                            0,
                            chartArea.top,
                            0,
                            chartArea.bottom
                        );
                        gradient.addColorStop(0, '#34d399FF');
                        gradient.addColorStop(0.4, '#34d39999');
                        gradient.addColorStop(1, '#34d39910');
                        return gradient;
                    },
                    borderColor: '#34d399',
                    borderWidth: 1.5,
                    borderRadius: 4,
                    borderSkipped: false,
                    grouped: true,
                    categoryPercentage: 0.6,
                    barPercentage: 0.8,
                    maxBarThickness: 40
                });

            } else if (metric === 'Sundry Creditors') {
                c = getChartDataByMetric(2 ,'Sundry Creditors');
                datasets.push({
                    label: 'Creditors',
                    data: c.out || [],
                    backgroundColor: function(ctx) {
                        const chart = ctx.chart;
                        const { ctx:c, chartArea } = chart;
                        if (!chartArea) return '#f472b6';
                        const gradient = c.createLinearGradient(
                            0,
                            chartArea.top,
                            0,
                            chartArea.bottom
                        );
                        gradient.addColorStop(0, '#f472b6FF');
                        gradient.addColorStop(0.4, '#f472b699');
                        gradient.addColorStop(1, '#f472b610');
                        return gradient;
                    },
                    // borderColor: '#f472b6',
                    borderColor: '#ec4899',
                    borderWidth: 1.5,
                    borderRadius: 4,
                    borderSkipped: false,
                    grouped: true,
                    categoryPercentage: 0.6,
                    barPercentage: 0.8,
                    maxBarThickness: 40
                });
            }
            /*
            |--------------------------------------------------------------------------
            | NORMAL BAR CHART
            |--------------------------------------------------------------------------
            */
            if (datasets.length > 0) {
                if (compare !== 'none') {
                    // let compareData = [];
                    // let compareLabel = '';
                    const useInSeries = metric === 'Cash-in-Hand' || metric === 'Sundry Debtors';


                     if (compare === 'prev-quarter') {
                        // const source = (useInSeries ? (c.in || []) : (c.out || [])).map(v => Number(v) || 0);
                        // const prevYearSource = (useInSeries ? (c.prevYearIn || []) : (c.prevYearOut || [])).map(v => Number(v) || 0);

                        // const quarterTotals = (arr = []) => ([
                        //     (arr[0] || 0) + (arr[1] || 0) + (arr[2] || 0),
                        //     (arr[3] || 0) + (arr[4] || 0) + (arr[5] || 0),
                        //     (arr[6] || 0) + (arr[7] || 0) + (arr[8] || 0),
                        //     (arr[9] || 0) + (arr[10] || 0) + (arr[11] || 0)
                        // ]);

                        // const currentQuarterSeries = quarterTotals(source);
                        // const previousYearSeries = quarterTotals(prevYearSource);
                        // const previousQuarterSeries = [
                        //     currentQuarterSeries[3] || 0,
                        //     currentQuarterSeries[0] || 0,
                        //     currentQuarterSeries[1] || 0,
                        //     currentQuarterSeries[2] || 0
                        // ];
                        let currentQuarterSeries = [];
                        let previousQuarterSeries = [];
                        let previousYearSeries = [];
                        const quarterCompare = Array.isArray(c?.quarterCompare) ? c.quarterCompare : [];

                        if (quarterCompare.length > 0) {
                            const amountKey = useInSeries ? 'in' : 'out';
                            previousQuarterSeries = quarterCompare.map(q => Number(q?.bars?.[0]?.[amountKey]) || 0);
                            currentQuarterSeries = quarterCompare.map(q => Number(q?.bars?.[1]?.[amountKey]) || 0);
                            previousYearSeries = quarterCompare.map(q => Number(q?.bars?.[2]?.[amountKey]) || 0);
                        } else {
                            const source = (useInSeries ? (c.in || []) : (c.out || [])).map(v => Number(v) || 0);
                            const prevYearSource = (useInSeries ? (c.prevYearIn || []) : (c.prevYearOut || [])).map(v => Number(v) || 0);

                            const quarterTotals = (arr = []) => ([
                                (arr[0] || 0) + (arr[1] || 0) + (arr[2] || 0),
                                (arr[3] || 0) + (arr[4] || 0) + (arr[5] || 0),
                                (arr[6] || 0) + (arr[7] || 0) + (arr[8] || 0),
                                (arr[9] || 0) + (arr[10] || 0) + (arr[11] || 0)
                            ]);

                            currentQuarterSeries = quarterTotals(source);
                            previousYearSeries = quarterTotals(prevYearSource);
                            previousQuarterSeries = [
                                currentQuarterSeries[3] || 0,
                                currentQuarterSeries[0] || 0,
                                currentQuarterSeries[1] || 0,
                                currentQuarterSeries[2] || 0
                            ];
                        }
                        datasets = [
                            {
                                label: 'Previous Quarter',
                                data: previousQuarterSeries,
                                backgroundColor: '#22d3ee66',
                                // borderColor: '#22d3ee',
                                borderColor: '#06b6d4',
                                borderWidth: 1.5,
                                borderRadius: 4,
                                borderSkipped: false,
                                grouped: true,
                                categoryPercentage: 0.6,
                                barPercentage: 0.8,
                                maxBarThickness: 40
                            },
                            {
                                label: 'Current Quarter',
                                data: currentQuarterSeries,
                                backgroundColor: '#34d39966',
                                borderColor: '#34d399',
                                borderWidth: 1.5,
                                borderRadius: 4,
                                borderSkipped: false,
                                grouped: true,
                                categoryPercentage: 0.6,
                                barPercentage: 0.8,
                                maxBarThickness: 40
                            },
                            {
                                label: 'Previous Year',
                                data: previousYearSeries,
                                backgroundColor: '#fbbf2466',
                                borderColor: '#fbbf24',
                                borderWidth: 1.5,
                                borderRadius: 4,
                                borderSkipped: false,
                                grouped: true,
                                categoryPercentage: 0.6,
                                barPercentage: 0.8,
                                maxBarThickness: 40
                            }
                        ];
                    } else {
                        let compareData = [];
                        let compareLabel = '';

                        if (compare === 'prev-year') {
                            compareData = useInSeries ? (c.prevYearIn || []) : (c.prevYearOut || []);
                            compareLabel = 'Previous Year';
                        } else if (compare === 'prev-month') {
                            compareData = useInSeries ? (c.prevMonthIn || []) : (c.prevMonthOut || []);
                            compareLabel = 'Previous Month';
                        }
                        if (compareData.length > 0) {
                            const compareColor = '#22d3ee';
                            datasets.push({
                                label: `${datasets[0].label} (${compareLabel})`,
                                data: compareData.map(v => Number(v) || 0),
                                backgroundColor: compareColor + '66',
                                borderColor: compareColor,
                                borderWidth: 1.5,
                                borderRadius: 4,
                                borderSkipped: false,
                                grouped: true,
                                categoryPercentage: 0.6,
                                barPercentage: 0.8,
                                maxBarThickness: 40
                            });
                        }
                    }

                    // if (compareData.length > 0) {
                    //     const compareColor = '#22d3ee';
                    //     datasets.push({
                    //         label: `${datasets[0].label} (${compareLabel})`,
                    //         data: compareData.map(v => Number(v) || 0),
                    //         backgroundColor: compareColor + '66',
                    //         borderColor: compareColor,
                    //         borderWidth: 1.5,
                    //         borderRadius: 4,
                    //         borderSkipped: false,
                    //         grouped: true,
                    //         categoryPercentage: 0.6,
                    //         barPercentage: 0.8,
                    //         maxBarThickness: 40
                    //     });
                    // }
                }   
                mainChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets
                    },
                    options: getChartOptions()
                });
                return;
            }
            /*
            |--------------------------------------------------------------------------
            | EXISTING CUMULATIVE CHART
            |--------------------------------------------------------------------------
            */
            const cumulativeDatasets = renderCumulativeBarChart(c, metric);
            mainChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: cumulativeDatasets
                },
                options: getChartOptions()
            });
            return;
        }
        /*
        |--------------------------------------------------------------------------
        | ADVANCED VIEW
        |--------------------------------------------------------------------------
        */
        renderLineChart(
            ctx,
            c,
            labels,
            metric,
            compare
        );
    } finally {

        setTimeout(() => {
            isRenderingChart = false;
        }, 100);
    }
}

        function initChart() {
            
            if (mainChart) {
                mainChart.destroy();
                mainChart = null;
            }
            const canvas = document.getElementById('mainChart');
            const ctx = canvas.getContext('2d');
            if (!ctx) {
                console.error('Canvas not found');
                return;
            }
            
            mainChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                }
            });
        }

        // Initialize chart and event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // console.log('DOM loaded, initializing chart...');

            //updateTabActiveState(activeType);
            renderChartFor(activeType, 'Sales Accounts', 'none'); // renderChartFor(activeType, '', 'none');

            // Set default selections
            // document.getElementById('metricSelect').value = '';
            document.getElementById('metricSelect').value = 'Sales Accounts';
            document.getElementById('compareSelect').value = 'none';

            // Event listeners
            document.getElementById('metricSelect').addEventListener('change', e => {
                const metric = e.target.value;
                const compareWith = document.getElementById('compareSelect').value;
                renderChartFor(activeType, metric, compareWith);
            });

            document.getElementById('compareSelect').addEventListener('change', e => {
                const compareWith = e.target.value;
                const metric = document.getElementById('metricSelect').value;
                renderChartFor(activeType, metric, compareWith);
            });

            // Tab button click handlers with reset functionality
            // Tab button click handlers: update only main chart
            document.querySelectorAll('[data-type]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const type = Number(this.dataset.type);

                    // RESET DROPDOWNS as per requirement
                    // resetDropdowns();

                    // Update active type and UI
                    activeType = type;
                    // IMMEDIATELY rebuild the chart with default settings
                    // renderChartFor(type, '', 'none');
                    const metric = document.getElementById('metricSelect')?.value || '';
                    const compareWith = document.getElementById('compareSelect')?.value || 'none';
                    renderChartFor(type, metric, compareWith);
                });
            });
            handleTypeChange('sales');
        });
        
    </script>
    <script>
        const glowPlugin = {
            id: 'glow',
            beforeDatasetDraw(chart, args) {
                const {
                    ctx
                } = chart;
                const dataset = chart.data.datasets[args.index];

                // 🔥 ONLY APPLY GLOW TO LINE (not fill)
                if (dataset.type !== 'bar') {
                    ctx.save();
                    ctx.shadowColor = dataset.borderColor;
                    ctx.shadowBlur = 20; // controlled glow
                    ctx.lineWidth = dataset.borderWidth;
                }
            },
            afterDatasetDraw(chart, args) {
                if (chart.data.datasets[args.index].type !== 'bar') {
                    chart.ctx.restore();
                }
            }
        };

        Chart.register(glowPlugin);

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

        let plData = <?php echo json_encode($plData ?? [], 15, 512) ?>;
        //let plData = <?php echo json_encode($pl ?? [], 15, 512) ?>;

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
        // const cogs = Number(plData.COGS || 0);

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
                datasets: [{
                    data: [
                        sales,
                        cogs,
                        //purchase,
                        directIncome,
                        indirectIncome,
                        directExpense,
                        indirectExpense,
                        Math.abs(net)
                    ],
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
                    // borderColor: 'rgba(0,0,0,0.15)',
                    borderColor: colors,
                    borderWidth: 1.5,
                    hoverOffset: 8  
                }]
            },
            plugins: [glowPlugin],
            cutout: '70%',
            radius: '90%',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
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
                cutout: '70%',
                radius: '90%',
                layout: {
                    padding: 0
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return context[0].label.replace(/\s+/g, ' ');
                            }
                        }
                    }
                }
            }
        });


        //let bsData = JSON.parse('<?php echo json_encode($bsData ?? []); ?>');

        // let rows = bsData.rows || [];
        // Assets (DR → make positive)
        let assets = Math.abs(Number(<?php echo e($assets ?? 0); ?>));
        // Liabilities
        let liabilities = Number(<?php echo e($liabs); ?>);
        // Equity (Capital)
        let equity = Number(<?php echo e($equity); ?>);
        // Total for center
        let totalAssets = assets;
        const bs_colors = ['#22d3ee', '#fbbf24', '#a78bfa'];
        // ===== BALANCE SHEET PIE =====
        new Chart(document.getElementById('bsPie'), {
            type: 'doughnut',
            data: {
                labels: [
                    'Assets',
                    'Liabilities',
                    'Equity'
                ],
                datasets: [{
                    data: [
                        assets,
                        liabilities,
                        equity
                    ],
                    // backgroundColor: [
                    //     '#22d3ee', // assets (cyan)
                    //     '#fbbf24', // liabilities (yellow)
                    //     '#a78bfa' // equity (violet)
                    // ]
                    backgroundColor: (ctx) => {
                        const chart = ctx.chart;
                        const {
                            ctx: c,
                            chartArea
                        } = chart;
                        if (!chartArea) return;

                        return bs_colors.map(color => {
                            const gradient = c.createLinearGradient(0, chartArea.top, 0,
                                chartArea.bottom);

                            gradient.addColorStop(0, color + 'FF');
                            gradient.addColorStop(0.4, color + 'AA');
                            gradient.addColorStop(0.7, color + '55');
                            gradient.addColorStop(1, color + '10');

                            return gradient;
                        });
                    },
                    // borderColor: 'rgba(0,0,0,0.15)',
                    borderColor: bs_colors,
                    borderWidth: 1.5,
                    hoverOffset: 8
                }]
            },
            plugins: [glowPlugin],
            cutout: '70%',
            radius: '90%',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
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
                cutout: '70%',
                radius: '90%',
                layout: {
                    padding: 0
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return context[0].label.replace(/\s+/g, ' ');
                            }
                        }
                    }
                }
            }
        });

        let plLabels = [
            'Revenue',
            'Cost of Revenue',
            'Direct Income',
            'Indirect Income',
            'Direct Expense',
            'Indirect Expense',
            isProfit ? 'Profit' : 'Loss' // 🔥 ADD
        ];

        let plValues = [
            sales,
            cogs,
            directIncome,
            indirectIncome,
            directExpense,
            indirectExpense,
            Math.abs(net)
        ];

        let plColors = [
            '#22d3ee',
            '#a78bfa',
            '#fbbf24',
            '#f472b6',
            '#d47171',
            '#a5ef44',
            isProfit ? '#22c55e' : '#ef4444'
        ];

        renderLegend('plLegend', plLabels, plValues, plColors);

        let bsLabels = ['Assets', 'Liabilities', 'Equity'];

        let bsValues = [
            assets,
            liabilities,
            equity
        ];

        let bsColors = ['#22d3ee', '#fbbf24', '#a78bfa'];

        renderLegend('bsLegend', bsLabels, bsValues, bsColors);

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

                    <span class="truncate text-gray-700 dark:text-gray-300">
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
    </script>
    <script>
        let chartData = {
            sales: <?php echo json_encode($charts[0]['in'] ?? [], 15, 512) ?>,
            purchase: <?php echo json_encode($charts[0]['out'] ?? [], 15, 512) ?>,

            direct_income: <?php echo json_encode($charts[1]['in'] ?? [], 15, 512) ?>,
            indirect_income: <?php echo json_encode($charts[2]['in'] ?? [], 15, 512) ?>,

            direct_expense: <?php echo json_encode($charts[1]['out'] ?? [], 15, 512) ?>,
            indirect_expense: <?php echo json_encode($charts[2]['out'] ?? [], 15, 512) ?>
        };

        let months = <?php echo json_encode($charts[0]['months'] ?? [], 15, 512) ?>;
        const textColor = isDarkMode() ? '#e5e7eb' : '#000000'; // white / black
        const gridColor = isDarkMode() ? '#000000' : '#e5e7eb'; // dark / light grid
        
        function isDarkMode() {
            return document.documentElement.classList.contains('dark');
        }

        function getLegendTextColor() {
            return isDarkMode() ? '#d1d5db' : '#374151';
        }


        function formatYAxis(value) {
            // value = Number(value);

            // if (value >= 10000000) return (value / 10000000) + 'Cr';
            // if (value >= 100000) return (value / 100000) + 'L';
            // if (value >= 1000) return (value / 1000) + 'K';

            // return value;
            return formatCompactAmount(value);
        }


        function updateSalesChart(type) {
            // map data based on type
            console.log("Selected type:", type);

            // Example:
            if (type === 'purchase') {
                // reload chart with purchase data
            }
        }

        document.getElementById('viewType').addEventListener('change', function() {

            const type = Number(this.value);

            console.log('Dropdown changed, type:', type);

            // SAME AS TAB CLICK
            // SAME AS TAB CLICK: redraw only main chart
            activeType = type;

            // 🔥 RESET DROPDOWNS (YOU MISSED THIS)
            // document.getElementById('metricSelect').value = '';
            // document.getElementById('compareSelect').value = 'none';

            // 🔥 UPDATE HIDDEN FIELD (IMPORTANT FOR FORM)
            const fyType = document.getElementById('fy_type');
            if (fyType) fyType.value = type;

            // 🔥 CALL CHART
            // renderChartFor(activeType, '', 'none');
            const selectedMetric = document.getElementById('metricSelect')?.value || '';
            const selectedCompare = document.getElementById('compareSelect')?.value || 'none';
            renderChartFor(activeType, selectedMetric, selectedCompare);
        });

        function handleRangeChange(value) {

            let from = '';
            let to = '';

            if (value === 'current_year') {
                from = "<?php echo e($currStart->format('Y-m-d')); ?>";
                to   = "<?php echo e($currEnd->format('Y-m-d')); ?>";
            }

            if (value === 'last_year') {
                from = "<?php echo e($lastStart->format('Y-m-d')); ?>";
                to   = "<?php echo e($lastEnd->format('Y-m-d')); ?>";
            }

            if (value === 'custom') {
                // don't auto set → user will pick manually
                return;
            }

            document.getElementById('fy_from').value = from;
            document.getElementById('fy_to').value = to;

            document.getElementById('graphForm').submit();
        }
        
function metricDropdown() {

    return {
        open: false,
        // selected: 'sales',
        selected:'Sales Accounts',
        options: {},
        chartOptions: {
            // sales: 'Sales',
            // purchase: 'Purchase',
            // receipt: 'Receipt',
            // payment: 'Payment',
            // cash: 'Cash',
            // bank: 'Bank',
            // creditor: 'Creditors',
            // debtor: 'Debtors',
            'Sales Accounts': 'Sales',
            'Purchase Accounts': 'Purchase',
            'Rcpt': 'Receipt',
            'Pymt': 'Payment',
            'Cash-in-Hand': 'Cash',
            'Bank Accounts': 'Bank',
            'Sundry Debtors': 'Sundry Debtors',
            'Sundry Creditors': 'Sundry Creditors'
        },

        advancedOptions: {
            sales_purchase: 'Sales vs Purchase',
            receipt_payment: 'Receipt vs Payment',
            creditors_debtors: 'Creditors vs Debtors',
            cash_bank: 'Cash & Bank'
        },

        init() {

            window.metricDropdownInstance = this;

            this.options = this.chartOptions;

            this.$watch('selected', value => {

                currentMetric = value;

                if (!isChartInitialized) return;

                renderChartFor(
                    activeType,
                    value,
                    currentCompare
                );
            });
        },

        updateView(view) {

            console.log('UPDATING VIEW:', view);

            if (view === 'advanced') {

                this.options = this.advancedOptions;

                // this.selected = 'sales_purchase';
                if (!Object.prototype.hasOwnProperty.call(this.options, this.selected)) {
                    this.selected = 'sales_purchase';
                }

            } else {

                this.options = this.chartOptions;

                // this.selected = 'sales';
                if (!Object.prototype.hasOwnProperty.call(this.options, this.selected)) {
                    this.selected = 'Sales Accounts';
                }
            }
        }
    }
}

function handleViewChange(view) {

    currentView = view;

    console.log('VIEW CHANGED:', view);

    if (window.metricDropdownInstance) {

        window.metricDropdownInstance.updateView(view);

        currentMetric =
            window.metricDropdownInstance.selected;
    }
    
    renderChartFor(
        activeType,
        currentMetric,
        currentCompare
    );
}

        function renderAdvancedLineChart(c, metric, compare) {

            // const existingChart = Chart.getChart("mainChart");

            // if (existingChart) {
            //     existingChart.destroy();
            // }

            const canvas = document.getElementById('mainChart');

            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            let inData = [];
            let outData = [];

            let inLabel = '';
            let outLabel = '';

            switch(metric){

                case 'sales_purchase':

                    inData = c.in || [];
                    outData = c.out || [];

                    inLabel = 'Sales';
                    outLabel = 'Purchase';

                    break;

                case 'receipt_payment':

                    inData = c.in || [];
                    outData = c.out || [];

                    inLabel = 'Receipt';
                    outLabel = 'Payment';

                    break;

                case 'creditors_debtors':

                    inData = c.in || [];
                    outData = c.out || [];

                    inLabel = 'Debtors';
                    outLabel = 'Creditors';

                    break;

                case 'cash_bank':

                    inData = c.in || [];
                    outData = c.out || [];

                    inLabel = 'Cash';
                    outLabel = 'Bank';

                    break;
            }

            const gradientIn = ctx.createLinearGradient(0, 0, 0, 350);
            gradientIn.addColorStop(0, 'rgba(34,211,238,0.45)');
            gradientIn.addColorStop(1, 'rgba(34,211,238,0.02)');

            const gradientOut = ctx.createLinearGradient(0, 0, 0, 350);
            gradientOut.addColorStop(0, 'rgba(168,85,247,0.45)');
            gradientOut.addColorStop(1, 'rgba(168,85,247,0.02)');

            const datasets = [];

            datasets.push({
                label: inLabel,
                data: inData,
                //borderColor: '#22d3ee',
                borderColor: '#06b6d4',
                backgroundColor: gradientIn,
                fill: true,
                tension: 0.45,
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            });

            datasets.push({
                label: outLabel,
                data: outData,
                borderColor: '#a855f7',
                backgroundColor: gradientOut,
                fill: true,
                tension: 0.45,
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            });
            if (mainChart) {
                mainChart.destroy();
                mainChart = null;
            }
            mainChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: c.labels || [],
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            labels: {
                                // color: '#d1d5db'
                                color: getLegendTextColor(),
                                usePointStyle: true,
                                pointStyle: 'rectRounded',
                                boxWidth: 30,
                                boxHeight: 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return context[0].label.replace(/\s+/g, ' ');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#d1d5db'
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.05)'
                            }
                        },
                        y: {
                             display: true,
                            beginAtZero: true,
                            ticks: {
                                color: '#d1d5db',
                                // callback: value => '₹ ' + fmt(value)
                                callback: value => '₹ ' + formatCompactAmount(value)
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.05)'
                            }
                        }
                    }
                }
            });
        }

        function renderSimpleBarChart(c, metric) {

            // const existingChart = Chart.getChart("mainChart");

            // if (existingChart) {
            //     existingChart.destroy();
            // }

            const canvas = document.getElementById('mainChart');
            const ctx = canvas.getContext('2d');

            const labels = c.labels || [];

            // 🔥 THIS CALLS YOUR EXISTING FUNCTION
            const datasets = renderCumulativeBarChart(c, {}, metric);
            // renderMainBarChart(c, metric);
            if (mainChart) {
                mainChart.destroy();
                mainChart = null;
            }
            mainChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.map(l => Array.isArray(l) ? l.join(' ') : l),
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: {
                            labels: {
                                // color: '#d1d5db'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return context[0].label.replace(/\s+/g, ' ');
                                }
                            }
                        }
                    },

                    scales: {

                        x: {
                            ticks: {
                                color: '#d1d5db'
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.05)'
                            }
                        },

                        y: {
                             display: true,
                            beginAtZero: true,
                            ticks: {
                                color: '#d1d5db',
                                // callback: value => '₹ ' + fmt(value)
                                callback: value => '₹ ' + formatCompactAmount(value)
                            },

                            grid: {
                                color: 'rgba(255,255,255,0.05)'
                            }
                        }
                    }
                }
            });
        }

        function renderMainBarChart(c, metric) {
            // const existingChart = Chart.getChart("mainChart");
            // if (existingChart) {
            //     existingChart.destroy();
            // }
            const canvas = document.getElementById('mainChart');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const labels = c.labels || [];
            // 🔥 GET DATASETS
            const datasets = renderCumulativeBarChart(c, {}, metric);
            // renderMainBarChart(c, metric);
            // 🔥 IMPORTANT
            if (!datasets || datasets.length === 0) {
                // console.log('No datasets found');
                return;
            }
            if (mainChart) {
                mainChart.destroy();
                mainChart = null;
            }
            mainChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.map(l => Array.isArray(l) ? l.join(' ') : l),
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            labels: {
                                // color: '#d1d5db'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return context[0].label.replace(/\s+/g, ' ');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#d1d5db'
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.05)'
                            }
                        },
                        y: {
                             display: true,
                            beginAtZero: true,
                            ticks: {
                                color: '#d1d5db',
                                // callback: value => '₹ ' + fmt(value)
                                callback: value => '₹ ' + formatCompactAmount(value)
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.05)'
                            }
                        }
                    }
                }
            });
        }

        function handleTypeChange(type) {

            console.log('Selected Type:', type);

            const canvas = document.getElementById('salesBar');

            if (!canvas) {
                console.error('salesBar canvas not found');
                return;
            }

            //const ctx = canvas.getContext('2d');
            // destroy old chart
            if (salesChart) {
                salesChart.destroy();
            }

            // dynamic data
            let labels = [];
            let data = [];
            let title = '';
            let color = '#22d3ee';
            
            switch(type){

                case 'sales':
                    labels = chartMap.salesPurchase?.months  || [];
                    data = chartMap.salesPurchase?.in  || [];
                    title = 'Sales';
                    color = '#22d3ee';
                    break;

                case 'purchase':
                    labels = chartMap.salesPurchase?.months || [];
                    data = chartMap.salesPurchase?.out  || [];
                    title = 'Purchase';
                    color = '#a78bfa';
                    break;

                case 'direct_income':
                    labels = chartMap.receiptPayment?.months  || [];
                    data = chartMap.incomeExpense.directIncome || [];
                    title = 'Direct Income';
                    color = '#34d399';
                    break;

                case 'indirect_income':
                    
                    labels = chartMap.cashBank?.months  || [];
                    data = chartMap.incomeExpense.indirectIncome || [];
                    title = 'Indirect Income';
                    color = '#fbbf24';
                    break;

                case 'direct_expense':
                    labels = chartMap.receiptPayment?.months  || [];
                    data = chartMap.incomeExpense.directExpense || [];
                    title = 'Direct Expense';
                    color = '#f472b6';
                    break;

                case 'indirect_expense':
                    labels = chartMap.cashBank?.months  || [];
                    data = chartMap.incomeExpense.indirectExpense  || [];
                    title = 'Indirect Expense';
                    color = '#60a5fa';
                    break;
            }

            // update title
            document.getElementById('chartTitle').innerText = title;

            // create chart
            salesChart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: labels.map(l => Array.isArray(l) ? l.join(' ') : l),
                    datasets: [{
                        label: title,
                        data: data,
                        backgroundColor: function(context) {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return color;
                            const gradient = ctx.createLinearGradient(
                                0,
                                chartArea.top,
                                0,
                                chartArea.bottom
                            );
                            gradient.addColorStop(0, color + 'FF');
                            gradient.addColorStop(0.5, color + '66');
                            gradient.addColorStop(1, color + '10');
                            return gradient;
                        },
                        borderColor: color,
                        borderWidth: 1.5,
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return context[0].label.replace(/\s+/g, ' ');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#9ca3af'
                            }
                        },
                        y: {
                             display: true,
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255,255,255,0.05)'
                            },
                            ticks: {
                                color: '#9ca3af',
                                // callback: value => '₹ ' + fmt(value)
                                callback: value => '₹ ' + formatCompactAmount(value)
                            }
                        }
                    }
                }
            });
        }

        function renderSalesChart(type) {

            //const ctx = document.getElementById('salesBar');
            const canvas = document.getElementById('salesBar');
            const c = byKey[activeType] || charts[0];

            if (!c) return;

            if (salesChart) {
                salesChart.destroy();
            }

            let data = [];
            let label = '';
            let color = '#22d3ee';

            switch (type) {

                case 'sales':
                    data = c.in || [];
                    label = 'Sales';
                    color = '#22d3ee';
                    break;

                case 'purchase':
                    data = c.out || [];
                    label = 'Purchase';
                    color = '#a78bfa';
                    break;

                case 'direct_income':
                    //data = c.in || [];   // 🔥 same source (if no separate data)
                    data: chartMap.incomeExpense.incomeExpense?.directIncome || [],
                    label = 'Direct Income';
                    color = '#34d399';
                    break;

                case 'indirect_income':
                    //data = c.in || [];
                    data: chartMap.incomeExpense.incomeExpense?.indirectIncome || [],
                    label = 'Indirect Income';
                    color = '#fbbf24';
                    break;

                case 'direct_expense':
                    // data = c.out || [];
                    data: chartMap.incomeExpense.incomeExpense?.directExpense || [],
                    label = 'Direct Expense';
                    color = '#f472b6';
                    break;

                case 'indirect_expense':
                    //data = c.out || [];
                    data: chartMap.incomeExpense.incomeExpense?.indirectExpense || [],
                    label = 'Indirect Expense';
                    color = '#60a5fa';
                    break;

                default:
                    data = c.in || [];
                    label = 'Sales';
            }

            salesChart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        // label: 'Sales',
                        // data: chartData.sales,
                        label: label,
                        data: data,
                        backgroundColor: (ctx) => {
                            const chart = ctx.chart;
                            const {
                                ctx: canvas
                            } = chart;

                            const gradient = canvas.createLinearGradient(0, 0, 0, 300);
                            // gradient.addColorStop(0, 'rgba(34,211,238,0.9)');
                            // gradient.addColorStop(0.5, 'rgba(34,211,238,0.5)');
                            // gradient.addColorStop(1, 'rgba(34,211,238,0.1)');

                            const baseColor = color || '#22d3ee';
                            const toRgba = (hex, alpha) => {
                                const v = hex.replace('#', '');
                                const n = parseInt(v, 16);
                                const r = (n >> 16) & 255;
                                const g = (n >> 8) & 255;
                                const b = n & 255;
                                return `rgba(${r},${g},${b},${alpha})`;
                            };
                            gradient.addColorStop(0, toRgba(baseColor, 0.9));
                            gradient.addColorStop(0.5, toRgba(baseColor, 0.5));
                            gradient.addColorStop(1, toRgba(baseColor, 0.1));

                            return gradient;
                        },
                        //borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                plugins: [glowPlugin], // ✅ ADD HERE
                cutout: '70%',
                radius: '90%',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
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
                    // scales: {
                    //     x: { ticks: { color: '#fff' } },
                    //     y: { ticks: { color: '#fff' } }
                    // },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#aaa' // ✅ FIX
                            }
                        },
                        y: {
                             display: true,
                            beginAtZero: true,
                            grid: {
                                display: false

                            },
                            ticks: {
                                color: '#aaa', // ✅ FIX
                                callback: v => formatYAxis(v)
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return context[0].label.replace(/\s+/g, ' ');
                                }
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        window.addEventListener('load', () => {

            isChartInitialized = true;

            currentView = 'chart';
            currentMetric = 'sales';

            renderChartFor(
                activeType,
                'Sales Accounts',
                'none'
            );
        });

        
function destroyMainChart() {

    try {

        const existingChart = Chart.getChart('mainChart');

        if (existingChart) {
            existingChart.destroy();
        }

        if (mainChart) {
            mainChart.destroy();
            mainChart = null;
        }

    } catch (e) {
        console.warn('Chart destroy warning:', e);
    }
}

function getChartOptions() {

    return {

        responsive: true,

        maintainAspectRatio: false,

        interaction: {
            mode: 'index',
            intersect: false
        },

        plugins: {

            legend: {

                labels: {
                    // color: '#ffffff'
                    color: getLegendTextColor(),
                    usePointStyle: true,
                    pointStyle: 'rectRounded',
                    boxWidth: 30,
                    boxHeight: 10
                }
            },
            tooltip: {
                callbacks: {
                    title: function(context) {
                        return context[0].label.replace(/\s+/g, ' ');
                    }
                }
            }
        },

        scales: {

            x: {

                ticks: {
                    color: '#9ca3af'
                },

                grid: {
                    color: 'rgba(255,255,255,0.05)'
                }
            },

            y: {

                beginAtZero: true,

                ticks: {

                    color: '#9ca3af',

                    callback: function(v) {
                        // return '₹ ' + fmt(v);
                        // callback: value => '₹ ' + formatCompactAmount(value);
                         return formatYAxis(v);
                    }
                },

                grid: {
                    color: 'rgba(255,255,255,0.05)'
                }
            }
        }
    };
}

function getQuarterSum(arr = [], startIndex = 0, endIndex = 2) {

    let total = 0;

    for (let i = startIndex; i <= endIndex; i++) {
        total += Number(arr[i] || 0);
    }

    return total;
}
function convertCumulativeToMonthly(arr = []) {

    if (!arr || !arr.length) return [];

    const result = [];

    for (let i = 0; i < arr.length; i++) {

        const current = Number(arr[i]) || 0;

        if (i === 0) {
            result.push(current);
        } else {

            const prev = Number(arr[i - 1]) || 0;

            result.push(current - prev);
        }
    }

    return result;
}
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/clients/reports/dashboard.blade.php ENDPATH**/ ?>