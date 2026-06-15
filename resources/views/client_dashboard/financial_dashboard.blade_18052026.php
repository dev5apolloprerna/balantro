    @php
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

    // Debug info (remove in production)
    \Log::info('Selected Groups Count:', ['count' => count($selectedGroups)]);
    \Log::info('Selected Groups with Balances Count:', ['count' => count($selectedGroupsWithBalances)]);

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
    foreach ($crRows as $r) {
        $amt = (float) $r->decMainAmount;

        $totalCr += $amt;

        if (in_array($r->strGroupName, ['Capital Account', 'Profit & Loss A/c'])) {
            $equity += $amt;
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
        
    @endphp

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

    <!-- @include('client_dashboard.topmenu') -->
    <div class="container py-0">

        <form id="graphForm" method="GET" action="{{ route('home') }}" class="rounded-lg">
            <input type="hidden" name="tab" value="financial">
            <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4">

                <div class="flex items-center gap-2">
                    <!-- <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Financial Year
                    </label> -->
                    @php
                        $isLastFY = $fromVal === $lastStart->format('Y-m-d') && $toVal === $lastEnd->format('Y-m-d');
                        $isCurrentFY = $fromVal === $currStart->format('Y-m-d') && $toVal === $currEnd->format('Y-m-d');
                        
                    @endphp
                    <!-- selected: '{{ $isLastFY ? 'last_year' : 'current_year' }}', -->
                    <div class="relative"
                        x-data="{
                            open: false,
                            
                            selected: '{{ $fyRangeSel  ?? 'current_year' }}',
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
                    <input type="hidden" id="fy_from" name="from" value="{{ $fromVal }}">
                    <input type="hidden" id="fy_to" name="to" value="{{ $toVal }}">
                    <input type="hidden" id="fy_type" name="type" value="{{ (int) ($activeType ?? 1) }}">
                </div>

            </div>
        </form>
    </div>

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


    <!-- Rest of your tabs and chart code remains the same -->
    @php
        $tabLabels = [
            1 => 'Sales vs Purchase',
            2 => 'Creditors vs Debtors',
            3 => 'Receipt vs Payment',
            4 => 'Cash & Bank Flow',
        ];
        $active = (int) ($activeType ?? 1);

    @endphp
    <div style="display: none;">
        Charts: {{ json_encode($charts ?? []) }}
        Active Type: {{ $activeType ?? 'not set' }}
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
                                document.getElementById('viewType').value = val;
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
                            viewType:'chart',
                            options:{
                                'none':'None',
                                'prev-month':'Previous Month',
                                'prev-quarter':'Previous Quarter',
                                'prev-year':'Previous Year'
                            },
                            init(){
                                this.viewType =
                                    document.getElementById('viewType').value;
                                this.$watch(() =>
                                    document.getElementById('viewType').value,
                                    value => {
                                        this.viewType = value;
                                        // reset compare in normal chart
                                        if(value === 'chart'){
                                            this.selected = 'none';
                                        }
                                    }
                                );
                                this.$watch('selected', val=>{
                                    const view =
                                        document.getElementById('viewType').value;
                                    // disable compare for normal chart
                                    if(view === 'chart'){
                                        return;
                                    }
                                    document.getElementById('compareSelect').value = val;
                                    if (!isChartInitialized) return;
                                    renderDynamicChart();
                                })
                            }
                        }"
                        :class="viewType === 'chart'
                            ? 'opacity-50 pointer-events-none'
                            : ''">

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
        <!-- <div class="flex flex-wrap items-center justify-between mb-0 gap-2">
            <div class="text-xs text-gray-700 dark:text-gray-300">
                In: <strong id="totIn" class="text-[#22d3ee] dark:text-[#22d3ee]">0.00</strong>
                &nbsp;|&nbsp;
                Out: <strong id="totOut" class="text-[#a78bfa] dark:text-[#a78bfa]">0.00</strong>
            </div>
        </div> -->
        <!-- <div id="mainChartWrapper" style="height:420px; width:100%; position:relative; min-height:420px;">
            <canvas id="mainChart" class="h-full w-full"></canvas>
        </div> -->
        
        <div class="h-80 w-full">
            <canvas id="mainChart"></canvas>

        </div>
    </div>

    <!-- Rest of your existing code -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <!-- ... existing report links ... -->
    </div>

    {{-- Dynamic Group Cards - IMPROVED WITH BETTER FALLBACKS --}}
    @php
        $fmt = fn($v) => number_format((float) $v, 2, '.', ',');
        $leftBar = fn($index) => $colorMap[array_keys($colorMap)[$index % count($colorMap)]];
        $chip = fn($index) => 'bg-gray-50 text-gray-600 dark:bg-gray-900/30 dark:text-gray-300';

        // Determine which groups to display
        $groupsToDisplay = [];
        if (!empty($selectedGroups)) {
            // User has selected groups
            $groupsToDisplay = $selectedGroups;
        } elseif (!empty($defaultGroupIds)) {
            // No user selection, use default groups
            $groupsToDisplay = $defaultGroupIds;
        }
    @endphp

    {{-- Dynamic Group Cards - USING allGroupCards WITH PROPER ICONS AND COLORS --}}
    @php
        $fmt = fn($v) => number_format((float) $v, 2, '.', ',');

        $leftBar = fn($accent) => match ($accent) {
            'blue' => 'bg-blue-500',
            'amber' => 'bg-amber-500',
            'violet' => 'bg-violet-500',
            'fuchsia' => 'bg-fuchsia-500',
            'teal' => 'bg-teal-500',
            'indigo' => 'bg-indigo-500',
            'emerald' => 'bg-emerald-500',
            'rose' => 'bg-rose-500',
            'orange' => 'bg-orange-500',
            'purple' => 'bg-purple-500',
            'cyan' => 'bg-cyan-500',
            'lime' => 'bg-lime-500',
            'yellow' => 'bg-yellow-500',
            'pink' => 'bg-pink-500',
            'sky' => 'bg-sky-500',
            'green' => 'bg-green-500',
            'red' => 'bg-red-500',
            'gray' => 'bg-gray-500',
            default => 'bg-gray-500',
        };

        $chip = fn($accent) => match ($accent) {
            'blue' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300',
            'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300',
            'violet' => 'bg-violet-50 text-violet-600 dark:bg-violet-900/30 dark:text-violet-300',
            'fuchsia' => 'bg-fuchsia-50 text-fuchsia-600 dark:bg-fuchsia-900/30 dark:text-fuchsia-300',
            'teal' => 'bg-teal-50 text-teal-600 dark:bg-teal-900/30 dark:text-teal-300',
            'indigo' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300',
            'emerald' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300',
            'rose' => 'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-300',
            'orange' => 'bg-orange-50 text-orange-600 dark:bg-orange-900/30 dark:text-orange-300',
            'purple' => 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-300',
            'cyan' => 'bg-cyan-50 text-cyan-600 dark:bg-cyan-900/30 dark:text-cyan-300',
            'lime' => 'bg-lime-50 text-lime-600 dark:bg-lime-900/30 dark:text-lime-300',
            'yellow' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-300',
            'pink' => 'bg-pink-50 text-pink-600 dark:bg-pink-900/30 dark:text-pink-300',
            'sky' => 'bg-sky-50 text-sky-600 dark:bg-sky-900/30 dark:text-sky-300',
            'green' => 'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-300',
            'red' => 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-300',
            'gray' => 'bg-gray-50 text-gray-600 dark:bg-gray-900/30 dark:text-gray-300',
            default => 'bg-gray-50 text-gray-600 dark:bg-gray-900/30 dark:text-gray-300',
        };
    @endphp
    @php
        // Maintain selected order (FIFO style)
        if (!empty($allGroupCards) && !empty($selectedGroups)) {

            $selectedOrder = array_flip($selectedGroups);

            usort($allGroupCards, function ($a, $b) use ($selectedOrder) {

                $aPos = $selectedOrder[$a['iGroupId']] ?? 999;
                $bPos = $selectedOrder[$b['iGroupId']] ?? 999;

                return $aPos <=> $bPos;
            });
        }
    @endphp
    @if (!empty($allGroupCards) && count($allGroupCards) > 0)
        <div class="mt-1">
            <div class="flex items-center justify-between mb-1">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Accounts Summary
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ count($allGroupCards) }} accounts
                        </span>
                    </h3>
                </div>

                <div class="relative" x-data="groupCustomizer({{ $selectedGroupsJson }}, {{ $allGroupIdsJson }}, {{ $defaultGroupIdsJson }})" x-init="init()">

                    <!-- BUTTON -->
                    <button type="button" @click="toggle()"
                        class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-black border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm hover:bg-gray-50">
                        <i class="fa-solid fa-layer-group"></i>
                        <span class="text-sm">Customize Groups</span>
                    </button>

                    <!-- DROPDOWN -->
                    <div x-show="open" x-transition @click.away="closeDropdown()"
                        class="fixed right-6 top-[140px] z-50 w-80 
                        bg-white dark:bg-black border border-gray-200 
                        dark:border-gray-600 rounded-lg shadow-xl flex flex-col"
                        style="height: 500px;">

                        <!-- HEADER -->
                        <div class="p-4 border-b border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between">
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">
                                    Select Groups to Display
                                </h3>
                                <button @click="closeDropdown()"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    ✕
                                </button>
                            </div>

                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Choose groups in multiples of 4 (4, 8, 12, 16, or 20)
                            </p>
                        </div>

                        <!-- SCROLLABLE LIST -->
                        <div class="overflow-y-auto p-4 space-y-2" style="flex:1; min-height:0;">

                            @foreach ($allGroups as $index => $group)
                                @php
                                    $colorIndex = $index % count($colorMap);
                                    $colorKeys = array_keys($colorMap);
                                    $color = $colorKeys[$colorIndex];
                                @endphp

                                <label
                                    class="flex items-center space-x-3 cursor-pointer p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                    :class="{
                                        'bg-blue-50 dark:bg-blue-900/20': selectedGroups.includes(
                                            {{ (int) $group->iGroupId }})
                                    }">

                                    <input type="checkbox" value="{{ (int) $group->iGroupId }}" x-model="selectedGroups"
                                        :disabled="isGroupDisabled({{ (int) $group->iGroupId }})"
                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded">

                                    <span class="text-sm text-gray-700 dark:text-gray-300 flex-1">
                                        {{ $group->strGroupName }}
                                    </span>

                                    <div class="w-3 h-3 rounded-full {{ $colorMap[$color] }}"></div>
                                </label>
                            @endforeach

                        </div>

                        <!-- STATUS -->
                        <div class="px-4 pt-2 pb-2 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex justify-between text-xs">
                                <span x-text="`${selectedGroups.length} selected`"></span>

                                <template x-if="getMultipleStatus().remainder !== 0">
                                    <span class="text-amber-500"
                                        x-text="`Need ${4 - getMultipleStatus().remainder} more`"></span>
                                </template>

                                <template x-if="getMultipleStatus().remainder === 0">
                                    <span class="text-green-500">✓ Valid</span>
                                </template>
                            </div>
                        </div>

                        <!-- FOOTER -->
                        <div class="p-3 border-t border-gray-200 dark:border-gray-600 bg-white dark:bg-black">
                            <div class="flex items-center justify-between">

                                <span class="text-xs text-gray-500 "
                                    x-text="`${selectedGroups.length} of 20 selected`"></span>

                                <div class="flex gap-2">

                                    <button type="button" @click="selectDefault()"
                                        class="px-3 py-1.5 text-xs border rounded">
                                        Default
                                    </button>

                                    <button type="button" @click="selectAll()"
                                        class="px-3 py-1.5 text-xs border rounded">
                                        Select All
                                    </button>

                                    <button type="button" @click="savePreferences()"
                                        :disabled="selectedGroups.length === 0 || selectedGroups.length % 4 !== 0"
                                        class="px-3 py-1.5 text-xs text-white bg-[#22d3ee] rounded disabled:opacity-50">
                                        Apply
                                    </button>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="cardsContainer">
                @foreach ($allGroupCards as $card)
                    <form method="GET" action="{{ route('reports.ledger') }}" class="card-form">
                        <button type="submit"
                            class="group block w-full text-left focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-xl card-hover color-{{ $loop->index % 6 }}">
                            <div
                                class="relative  rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[92px] transition-all duration-300 hover:shadow-md">
                                {{-- <div class="absolute inset-y-0 left-0 w-1.5 {{ $leftBar($card['accent']) }}">
                        </div> --}}

                                <div class="p-4 pl-6">
                                    <div class="flex items-start justify-between">
                                        <div class="pr-3 flex-1">
                                            <div
                                                class="text-[12px] uppercase tracking-wide text-gray-500 dark:text-gray-400 truncate">
                                                {{ $card['label'] }}
                                            </div>
                                            <div class="mt-0.5 text-xl md:text-2xl font-semibold leading-tight text-gray-900 dark:text-white tabular-nums"
                                                style="font-size: 1rem !important;">
                                                ₹ {{ $fmt($card['value']) }}
                                            </div>
                                        </div>

                                        <div class="shrink-0">
                                            {{-- <div
                                                class="h-9 w-9 md:h-10 md:w-10 rounded-full flex items-center justify-center {{ $chip($card['accent']) }} transition-colors group-hover:bg-opacity-80"> --}}
                                            {{-- <i class="{{ $card['icon'] }} text-sm md:text-base"></i> --}}
                                            <img src="{{ $card['icon'] }}" class="h-5 w-5 md:h-10 md:w-10 object-contain"
                                                alt="icon">
                                            {{-- </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </button>
                        <input type="hidden" name="group_id" value="{{ $card['iGroupId'] }}" />
                        <input type="hidden" name="from" value="{{ $fromVal }}">
                        <input type="hidden" name="to" value="{{ $toVal }}">
                    </form>
                @endforeach
            </div>
        </div>
    @elseif($allGroups->isNotEmpty() && !empty($selectedGroups))
        {{-- Fallback: If allGroupCards is empty but we have groups, create basic cards --}}
        @php
            $groupsToDisplay = $selectedGroups;
            $displayedCount = 0;
        @endphp

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="cardsContainer">
            @foreach ($groupsToDisplay as $index => $groupId)
                @php
                    $group = $allGroups->firstWhere('iGroupId', $groupId);
                    if (!$group) {
                        continue;
                    }
                    $displayedCount++;
                    $closingBalance = $group->Closing ?? 0;
                    $groupName = $group->strGroupName ?? 'Unknown Group';
                    $accentColor = $this->getAccentColor($groupName);
                    $groupIcon = $this->getGroupIcon($groupName);
                @endphp

                <form method="GET" action="{{ route('reports.ledger') }}" class="card-form">
                    <button type="submit"
                        class="group block w-full text-left focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-xl card-hover">
                        <div
                            class="relative bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[92px] transition-all duration-300 hover:shadow-md">
                            <div class="absolute inset-y-0 left-0 w-1.5 {{ $leftBar($accentColor) }}"></div>

                            <div class="p-4 pl-6">
                                <div class="flex items-start justify-between">
                                    <div class="pr-3 flex-1">
                                        <div
                                            class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400 truncate">
                                            {{ $groupName }}
                                        </div>
                                        <div class="mt-0.5 text-xl md:text-2xl font-semibold leading-tight text-gray-900 dark:text-white tabular-nums"
                                            style="font-size: 1rem !important;">
                                            ₹ {{ $fmt($closingBalance) }}
                                        </div>
                                    </div>

                                    <div class="shrink-0">
                                        <div
                                            class="h-9 w-9 md:h-10 md:w-10 rounded-full flex items-center justify-center {{ $chip($accentColor) }} transition-colors group-hover:bg-opacity-80">
                                            <i class="{{ $groupIcon }} text-sm md:text-base"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </button>
                    <input type="hidden" name="group_id" value="{{ $groupId }}" />
                    <input type="hidden" name="from" value="{{ $fromVal }}">
                    <input type="hidden" name="to" value="{{ $toVal }}">
                </form>
            @endforeach
        </div>

        @if ($displayedCount === 0)
            <div
                class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                    <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                    Selected groups not found in your account. Please use "Customize Groups" to select available groups.
                </p>
            </div>
        @endif
    @elseif($allGroups->isEmpty())
        <div
            class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                No groups found for your account. Please contact administrator.
            </p>
        </div>
    @else
        <div
            class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                No groups available. Please use "Customize Groups" to select groups to display.
            </p>
        </div>
    @endif

    <script>
        // Simple notification function for Alpine component
        function showNotification(message, type = 'info') {
            const alertClass = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 text-white rounded-lg shadow-lg ${alertClass}`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('groupCustomizer', (selectedGroups, allGroupIds, defaultGroupIds) => {
                return {
                    open: false,
                    toggle() {
                        this.open = !this.open;
                        document.body.style.overflow = this.open ? 'hidden' : '';
                    },
                    closeDropdown() {
                        this.open = false;
                        document.body.style.overflow = '';
                    },
                    selectedGroups: [],
                    allGroupIds: allGroupIds.map(id => parseInt(id)),
                    defaultGroupIds: defaultGroupIds.map(id => parseInt(id)),
                    maxGroups: 20,
                    SERVER_MAX_GROUPS: 20,

                    init() {
                        // Initialize selectedGroups with proper data
                        this.selectedGroups = selectedGroups.map(id => parseInt(id)).filter(id =>
                            this.allGroupIds.includes(id)
                        );

                        // If no groups are selected, use defaults
                        if (this.selectedGroups.length === 0) {
                            this.selectedGroups = [...this.defaultGroupIds].filter(id =>
                                this.allGroupIds.includes(id)
                            );
                        }
                    },

                    getMultipleStatus() {
                        const remainder = this.selectedGroups.length % 4;
                        return {
                            remainder: remainder,
                            needed: remainder === 0 ? 0 : 4 - remainder
                        };
                    },

                    isGroupDisabled(groupId) {
                        return !this.selectedGroups.includes(groupId) && this.selectedGroups.length >= this
                            .SERVER_MAX_GROUPS;
                    },

                    toggleGroup(groupId) {
                        const groupIdInt = parseInt(groupId);

                        if (this.selectedGroups.includes(groupIdInt)) {
                            // Remove group if already selected
                            this.selectedGroups = this.selectedGroups.filter(id => id !== groupIdInt);
                        } else {
                            // Add group if not at max limit
                            if (this.selectedGroups.length < this.SERVER_MAX_GROUPS) {
                                this.selectedGroups = [...this.selectedGroups, groupIdInt];
                            } else {
                                this.showNotification(`Maximum ${this.SERVER_MAX_GROUPS} groups allowed`,
                                    'error');
                            }
                        }
                    },

                    selectDefault() {
                        this.selectedGroups = [...this.defaultGroupIds]
                            .filter(id => this.allGroupIds.includes(id))
                            .slice(0, this.SERVER_MAX_GROUPS)
                            .map(id => parseInt(id));
                        this.showNotification('Default groups selected', 'info');
                    },

                    selectAll() {
                        this.selectedGroups = [...this.allGroupIds]
                            .slice(0, this.SERVER_MAX_GROUPS)
                            .map(id => parseInt(id));
                        this.showNotification('All groups selected', 'info');
                    },

                    async savePreferences() {
                        // Remove duplicates and ensure all are integers
                        const uniqueGroups = [...new Set(this.selectedGroups.map(id => parseInt(id)))];

                        // Filter out groups that don't exist in allGroupIds
                        const validGroups = uniqueGroups.filter(id => this.allGroupIds.includes(id));

                        // Client-side validation
                        if (validGroups.length === 0) {
                            this.showNotification('Please select at least one valid group', 'error');
                            return;
                        }

                        // Multiple of 4 validation
                        if (validGroups.length % 4 !== 0) {
                            this.showNotification(
                                `Number of selected groups must be a multiple of 4 (currently: ${validGroups.length})`,
                                'error'
                            );
                            return;
                        }

                        // Extra safety check with server constant
                        if (validGroups.length > this.SERVER_MAX_GROUPS) {
                            this.showNotification(`Maximum ${this.SERVER_MAX_GROUPS} groups allowed`,
                                'error');
                            return;
                        }

                        try {
                            const response = await fetch(
                                "{{ route('dashboard.save-card-preferences') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        selected_groups: validGroups
                                    })
                                });

                            const result = await response.json();
                            // console.log('Save response:', result);

                            if (result.success) {
                                this.showNotification('Groups updated successfully!', 'success');
                                //this.open = false;
                                this.closeDropdown(); // 🔥 IMPORTANT FIX

                                // Update the selectedGroups with the validated ones
                                this.selectedGroups = validGroups;

                                // Reload after a short delay to show the notification
                                setTimeout(() => {
                                    this.closeDropdown(); // 🔥 IMPORTANT FIX
                                    window.location.reload();
                                }, 1000);
                            } else {
                                this.showNotification(result.message || 'Failed to save preferences',
                                    'error');
                            }
                        } catch (error) {
                            console.error('Error saving preferences:', error);
                            this.showNotification('Network error - please try again', 'error');
                        }
                    },
                    showNotification(message, type = 'info') {
                        // Remove existing notifications
                        document.querySelectorAll('[data-group-notification]').forEach(el => el.remove());

                        const alertClass = type === 'success' ? 'bg-green-500' :
                            type === 'error' ? 'bg-red-500' : 'bg-blue-500';

                        const notification = document.createElement('div');
                        notification.setAttribute('data-group-notification', 'true');
                        notification.className =
                            `fixed top-4 right-4 z-50 px-6 py-3 text-white rounded-lg shadow-lg ${alertClass} transition-opacity duration-300`;
                        notification.textContent = message;
                        document.body.appendChild(notification);

                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.remove();
                            }
                        }, 3000);
                    }
                }
            });
        });

        function createDemoGroups() {
            window.location.reload();
        }
    </script>

    <script>
        // FY selection handling
        const form = document.getElementById('graphForm');
        const fySel = document.getElementById('fyKey');
        const hidFrom = form.querySelector('#fy_from');
        const hidTo = form.querySelector('#fy_to');
        const hidType = form.querySelector('#fy_type');

        // FY quick-fill: update hidden dates then submit
        if (fySel) {
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const charts = @json($charts ?? []);
        const metricConfig = {

            chart: {
                sales: {
                    type: 1,
                    mode: 'single',
                    chartType: 'bar',
                    cumulative: true,
                    label: 'Sales'
                },

                purchase: {
                    type: 1,
                    mode: 'single-out',
                    chartType: 'bar',
                    cumulative: true,
                    label: 'Purchase'
                },

                receipt: {
                    type: 3,
                    mode: 'single',
                    chartType: 'bar',
                    cumulative: true,
                    label: 'Receipt'
                },

                payment: {
                    type: 3,
                    mode: 'single-out',
                    chartType: 'bar',
                    cumulative: true,
                    label: 'Payment'
                },

                cash: {
                    type: 4,
                    mode: 'cash',
                    chartType: 'bar',
                    cumulative: false,
                    label: 'Cash'
                },

                bank: {
                    type: 4,
                    mode: 'bank',
                    chartType: 'bar',
                    cumulative: false,
                    label: 'Bank'
                },

                debtor: {
                    type: 2,
                    mode: 'single',
                    chartType: 'bar',
                    cumulative: false,
                    label: 'Sundry Debtor'
                },

                creditor: {
                    type: 2,
                    mode: 'single-out',
                    chartType: 'bar',
                    cumulative: false,
                    label: 'Sundry Creditor'
                }
            },

            advanced: {

                sales_purchase: {
                    type: 1,
                    chartType: 'line',
                    label: 'Sales V/S Purchase'
                },

                receipt_payment: {
                    type: 3,
                    chartType: 'line',
                    label: 'Receipt V/S Payment'
                },

                creditor_debtor: {
                    type: 2,
                    chartType: 'line',
                    label: 'Creditor V/S Debtor'
                },

                cash_bank: {
                    type: 4,
                    chartType: 'line',
                    label: 'Cash V/S Bank'
                }
            }
        };
        const chartMap = {
            salesPurchase: charts.find(x => Number(x.key) === 1),
            creditorsDebtors: charts.find(x => Number(x.key) === 2),
            receiptPayment: charts.find(x => Number(x.key) === 3),
            cashBank: charts.find(x => Number(x.key) === 4),
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

        let activeType = {{ (int) ($activeType ?? 1) }};
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
            const datasets = [];

            // console.log('Rendering CUMULATIVE BAR CHART for metric:', metric);

            // Get the data based on selected metric
            let currentData = [];
            let cumulativeData = [];
            let labelName = '';
            let colorCurrent = '#3b82f6';
            let colorCumulative = '#1e40af';

            if ((metric === 'Sales Accounts' || metric === 'sales') && c.in && c.in.length > 0) {
                currentData = c.in.map(v => Number(v) || 0);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Sales';
                colorCurrent = '#22d3ee'; // Light Blue
                colorCumulative = '#a78bfa'; // Dark Blue
            } else if ((metric === 'Purchase Accounts' || metric === 'purchase') && c.out && c.out.length > 0) {
                currentData = c.out.map(v => Number(v) || 0);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Purchase';
                colorCurrent = '#22d3ee'; // Light Orange
                colorCumulative = '#34d399'; // Dark Orange
            } else if (metric === 'Sundry Debtors' && c.in && c.in.length > 0) {
                currentData = c.in.map(v => Number(v) || 0);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Debtors';
                colorCurrent = '#22d3ee'; // Light Green
                colorCumulative = '#fbbf24'; // Dark Green
            } else if (metric === 'Sundry Creditors' && c.out && c.out.length > 0) {
                currentData = c.out.map(v => Number(v) || 0);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Creditors';
                colorCurrent = '#22d3ee'; // Light Purple
                colorCumulative = '#f472b6'; // Dark Purple
            } else if ((metric === 'Rcpt' || metric === 'receipt') && c.in && c.in.length > 0) {
                currentData = c.in.map(v => Number(v) || 0);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Receipts';
                colorCurrent = '#22d3ee'; // Light Cyan
                colorCumulative = '#60a5fa'; // Dark Cyan
            } else if ((metric === 'Pymt' || metric === 'payment') && c.out && c.out.length > 0) {
                currentData = c.out.map(v => Number(v) || 0);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Payments';
                colorCurrent = '#22d3ee'; // Light Red
                colorCumulative = '#a78bfa'; // Dark Red
            } else if ((metric === 'Cash-in-Hand' || metric === 'cash') && c.in?.length) {
                // For Cash, show both in and out
                if (c.in && c.in.length > 0) {
                    currentData = c.in.map(v => Number(v) || 0);
                    cumulativeData = calculateCumulativeData(currentData);
                    labelName = 'Cash In';
                    colorCurrent = '#22d3ee'; // Light Green
                    colorCumulative = '#34d399'; // Dark Green
                }
            } else if ((metric === 'Bank Accounts' || metric === 'bank') && c.in?.length) {
                // For Bank, show both in and out
                if (c.in && c.in.length > 0) {
                    currentData = c.in.map(v => Number(v) || 0);
                    cumulativeData = calculateCumulativeData(currentData);
                    labelName = 'Bank In';
                    colorCurrent = '#0ea5e9'; // Light Blue
                    colorCumulative = '#fbbf24'; // Dark Blue
                }
            }

            // If we have data, create the two bars
            if (currentData.length > 0) {
                // Current Month Bar
                datasets.push({
                    label: `${labelName} (Current Month)`,
                    data: currentData,
                    //backgroundColor: colorCurrent,
                    backgroundColor: function(ctx) {
                        const chart = ctx.chart;
                        const {
                            ctx: c,
                            chartArea
                        } = chart;
                        if (!chartArea) return colorCurrent;

                        const gradient = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);

                        gradient.addColorStop(0, colorCurrent + 'FF'); // bright top
                        gradient.addColorStop(0.4, colorCurrent + '99');
                        gradient.addColorStop(1, colorCurrent + '22'); // glass fade

                        return gradient;
                    },
                    //borderRadius: 12,
                    //borderSkipped: false,
                    // borderRadius: 12,
                    borderSkipped: false,
                    // borderWidth: 1.5,
                    plugins: [glowPlugin],

                    borderColor: colorCurrent,
                    borderWidth: 1,
                    borderRadius: 4,
                    categoryPercentage: 0.6,
                    barPercentage: 0.8
                });

                // Cumulative Bar
                datasets.push({
                    label: `${labelName} (Cumulative)`,
                    data: cumulativeData,
                    backgroundColor: function(ctx) {
                        const chart = ctx.chart;
                        const {
                            ctx: c,
                            chartArea
                        } = chart;
                        if (!chartArea) return colorCumulative;

                        const gradient = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);

                        gradient.addColorStop(0, colorCumulative + 'FF'); // strong top
                        gradient.addColorStop(0.3, colorCumulative + '99');
                        gradient.addColorStop(0.7, colorCumulative + '33');
                        gradient.addColorStop(1, colorCumulative + '05'); // soft fade

                        return gradient;
                    },
                    borderColor: colorCumulative,
                    borderWidth: 1.5,
                    borderRadius: 4,
                    plugins: [glowPlugin],
                    categoryPercentage: 0.6,
                    borderSkipped: false,
                    barPercentage: 0.8
                });
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
        default: return null;
    }
}

function renderLineChart(ctx, c, labels, metric, compare) {
    metric = normalizeMetric(metric);
    // let data1 = [];
    // let data2 = [];
    

    let label1 = '';
    let label2 = '';

    /*
    |--------------------------------------------------------------------------
    | METRIC MAPPING
    |--------------------------------------------------------------------------
    */
    let compareIn = [];
    let compareOut = [];
    switch (metric) {

        case 'sales_purchase':
            if (compare === 'prev-quarter') {
                labels = ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar']
                const makeQuarter = (arr = []) => {
                    return [
                        (arr[0] || 0) + (arr[1] || 0) + (arr[2] || 0),
                        (arr[3] || 0) + (arr[4] || 0) + (arr[5] || 0),
                        (arr[6] || 0) + (arr[7] || 0) + (arr[8] || 0),
                        (arr[9] || 0) + (arr[10] || 0) + (arr[11] || 0),
                    ];
                };

                data1 = makeQuarter(c.in || []);
                data2 = makeQuarter(c.out || []);

                compareIn = makeQuarter(c.prevMonthIn || []);
                compareOut = makeQuarter(c.prevMonthOut || []);
            } else {
                data1 = c.in || [];
                data2 = c.out || [];
                if (compare === 'prev-month') {
                    compareIn  = c.prevMonthIn || [];
                    compareOut = c.prevMonthOut || [];
                } else if (compare === 'prev-year') {
                    compareIn  = c.prevYearIn || [];
                    compareOut = c.prevYearOut || [];
                }
            }
            // data1 = c.in || [];
            // data2 = c.out || [];
            label1 = 'Sales';
            label2 = 'Purchase';
            break;

        case 'receipt_payment':
            if (compare === 'prev-quarter') {
                labels = ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar']
                const makeQuarter = (arr = []) => {
                    return [
                        (arr[0] || 0) + (arr[1] || 0) + (arr[2] || 0),
                        (arr[3] || 0) + (arr[4] || 0) + (arr[5] || 0),
                        (arr[6] || 0) + (arr[7] || 0) + (arr[8] || 0),
                        (arr[9] || 0) + (arr[10] || 0) + (arr[11] || 0),
                    ];
                };

                data1 = makeQuarter(c.in || []);
                data2 = makeQuarter(c.out || []);

                compareIn = makeQuarter(c.prevMonthIn || []);
                compareOut = makeQuarter(c.prevMonthOut || []);
            } else {
                data1 = c.in || [];
                data2 = c.out || [];
                if (compare === 'prev-month') {
                    compareIn  = c.prevMonthIn || [];
                    compareOut = c.prevMonthOut || [];
                } else if (compare === 'prev-year') {
                    compareIn  = c.prevYearIn || [];
                    compareOut = c.prevYearOut || [];
                }
            }
            // data1 = c.in || [];
            // data2 = c.out || [];
            label1 = 'Receipt';
            label2 = 'Payment';
            break;

        case 'creditors_debtors':
            if (compare === 'prev-quarter') {
                labels = ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar']
                const makeQuarter = (arr = []) => {
                    return [
                        (arr[0] || 0) + (arr[1] || 0) + (arr[2] || 0),
                        (arr[3] || 0) + (arr[4] || 0) + (arr[5] || 0),
                        (arr[6] || 0) + (arr[7] || 0) + (arr[8] || 0),
                        (arr[9] || 0) + (arr[10] || 0) + (arr[11] || 0),
                    ];
                };

                data1 = makeQuarter(c.in || []);
                data2 = makeQuarter(c.out || []);

                compareIn = makeQuarter(c.prevMonthIn || []);
                compareOut = makeQuarter(c.prevMonthOut || []);
            } else {
                data1 = c.in || [];
                data2 = c.out || [];
                if (compare === 'prev-month') {
                    compareIn  = c.prevMonthIn || [];
                    compareOut = c.prevMonthOut || [];
                } else if (compare === 'prev-year') {
                    compareIn  = c.prevYearIn || [];
                    compareOut = c.prevYearOut || [];
                }
            }
            // data1 = c.in || [];
            // data2 = c.out || [];
            label1 = 'Debtors';
            label2 = 'Creditors';
            break;

        case 'cash_bank':
            if (compare === 'prev-quarter') {
                labels = ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar']
                const makeQuarter = (arr = []) => {
                    return [
                        (arr[0] || 0) + (arr[1] || 0) + (arr[2] || 0),
                        (arr[3] || 0) + (arr[4] || 0) + (arr[5] || 0),
                        (arr[6] || 0) + (arr[7] || 0) + (arr[8] || 0),
                        (arr[9] || 0) + (arr[10] || 0) + (arr[11] || 0),
                    ];
                };

                data1 = makeQuarter(c.in || []);
                data2 = makeQuarter(c.out || []);

                compareIn = makeQuarter(c.prevMonthIn || []);
                compareOut = makeQuarter(c.prevMonthOut || []);
            } else {
                data1 = c.in || [];
                data2 = c.out || [];
                if (compare === 'prev-month') {
                    compareIn  = c.prevMonthIn || [];
                    compareOut = c.prevMonthOut || [];
                } else if (compare === 'prev-year') {
                    compareIn  = c.prevYearIn || [];
                    compareOut = c.prevYearOut || [];
                }
            }
            // data1 = c.in || [];
            // data2 = c.out || [];
            label1 = 'Cash';
            label2 = 'Bank';
            break;

        default:
            if (compare === 'prev-quarter') {
                labels = ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar']
                const makeQuarter = (arr = []) => {
                    return [
                        (arr[0] || 0) + (arr[1] || 0) + (arr[2] || 0),
                        (arr[3] || 0) + (arr[4] || 0) + (arr[5] || 0),
                        (arr[6] || 0) + (arr[7] || 0) + (arr[8] || 0),
                        (arr[9] || 0) + (arr[10] || 0) + (arr[11] || 0),
                    ];
                };

                data1 = makeQuarter(c.in || []);
                data2 = makeQuarter(c.out || []);

                compareIn = makeQuarter(c.prevMonthIn || []);
                compareOut = makeQuarter(c.prevMonthOut || []);
            } else {
                data1 = c.in || [];
                data2 = c.out || [];
                if (compare === 'prev-month') {
                    compareIn  = c.prevMonthIn || [];
                    compareOut = c.prevMonthOut || [];
                } else if (compare === 'prev-year') {
                    compareIn  = c.prevYearIn || [];
                    compareOut = c.prevYearOut || [];
                }
            }
            // data1 = c.in || [];
            // data2 = c.out || [];
            label1 = 'Inflow';
            label2 = 'Outflow';
            break;
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
            // backgroundColor: 'transparent',
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
            borderColor: '#a78bf8',
            //backgroundColor: 'transparent',
            backgroundColor: 'rgba(167, 139, 250, 0.16)',
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
        const ctx = document.getElementById('mainChart').getContext('2d');
        const gradientPrevIn = ctx.createLinearGradient(0, 0, 0, 400);
        gradientPrevIn.addColorStop(0, 'rgba(52,211,153,0.30)');
        gradientPrevIn.addColorStop(1, 'rgba(52,211,153,0.02)');
        const gradientPrevOut = ctx.createLinearGradient(0, 0, 0, 400);
        gradientPrevOut.addColorStop(0, 'rgba(253,230,138,0.30)');
        gradientPrevOut.addColorStop(1, 'rgba(253,230,138,0.02)');
        datasets.push({
            label: label1 + ' Previous',
            data: compareIn,
            borderColor: '#34d399',
            backgroundColor: gradientPrevIn,
            borderDash: [5, 5],
            tension: 0.45,
            borderWidth: 2,
            pointRadius: 3,
            fill: true
        });
        datasets.push({
            label: label2 + ' Previous',
            data: compareOut,
            borderColor: '#fde68a',
            backgroundColor: gradientPrevOut,
            borderDash: [5, 5],
            tension: 0.45,
            borderWidth: 2,
            pointRadius: 3,
            fill: true
        });
    }

    // if (compare !== 'none') {
    //     datasets.push({
    //         label: label1 + ' Previous',
    //         data: data1.map(v => v * 0.8),
    //         borderColor: '#34d399',
    //         borderDash: [5, 5],
    //         tension: 0.45,
    //         borderWidth: 2,
    //         pointRadius: 3,
    //         fill: false
    //     });
    //     datasets.push({
    //         label: label2 + ' Previous',
    //         data: data2.map(v => v * 0.8),
    //         borderColor: '#fde68a',
    //         borderDash: [5, 5],
    //         tension: 0.45,
    //         borderWidth: 2,
    //         pointRadius: 3,
    //         fill: false
    //     });
    // }

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
        const labels = compare === 'prev-quarter'
            ? (c.quarterLabels || ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar'])
            : (c.months || []);

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
                    borderColor: '#22d3ee',
                    borderWidth: 1.5,
                    borderRadius: 12,
                    borderSkipped: false
                });
            } else if (metric === 'Bank Accounts') {
                datasets.push({
                    label: 'Bank',
                    data: c.in || [],
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
                    borderRadius: 12,
                    borderSkipped: false
                });
            } else if (metric === 'Sundry Debtors') {
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
                    borderRadius: 12,
                    borderSkipped: false
                });

            } else if (metric === 'Sundry Creditors') {
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
                    borderColor: '#f472b6',
                    borderWidth: 1.5,
                    borderRadius: 12,
                    borderSkipped: false
                });
            }
            /*
            |--------------------------------------------------------------------------
            | NORMAL BAR CHART
            |--------------------------------------------------------------------------
            */
            if (datasets.length > 0) {
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
            // renderChartFor(activeType, '', 'none');
            renderDynamicChart();

            // Set default selections
            document.getElementById('metricSelect').value = '';
            document.getElementById('compareSelect').value = 'none';

            // Event listeners
            document.getElementById('metricSelect').addEventListener('change', e => {
                const metric = e.target.value;
                const compareWith = document.getElementById('compareSelect').value;
                // renderChartFor(activeType, metric, compareWith);
                renderDynamicChart();
            });

            document.getElementById('compareSelect').addEventListener('change', e => {
                const compareWith = e.target.value;
                const metric = document.getElementById('metricSelect').value;
                // renderChartFor(activeType, metric, compareWith);
                renderDynamicChart();
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
                    // renderChartFor(type, metric, compareWith);
                    renderDynamicChart();
                });
            });
            handleTypeChange('sales');
        });
    </script>
    <script>
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

        let plData = @json($plData ?? []);
        let sales = Number(plData.cr?.[0]?.decMainAmount || 0);

        let purchase = Number(
            plData.dr?.find(x => x.strGroupName === 'Purchase Accounts')?.decMainAmount || 0
        );

        let directExpense = Number(
            plData.dr?.find(x => x.strGroupName === 'Direct Expenses')?.decMainAmount || 0
        );

        let indirectExpense = Number(plData.IndirectExpenses?.[0]?.decMainAmount || 0);

        let indirectIncome = Number(plData.IndirectIncomes?.[0]?.decMainAmount || 0);

        let netProfit = Number(plData.NetPandL || 0);

        const totalIncome = sales + indirectIncome;
        const totalExpense = purchase + directExpense + indirectExpense;

        const net = totalIncome - totalExpense;
        const isProfit = net >= 0;

        // ===== PROFIT & LOSS PIE =====
        new Chart(document.getElementById('plPie'), {
            type: 'doughnut',
            data: {
                labels: [
                    'Total Income',
                    'COGS',
                    'Operating Expense',
                    'Other Expense',
                    isProfit ? 'Profit' : 'Loss' // 🔥 ADD
                ],
                datasets: [{
                    data: [
                        sales + indirectIncome, // Total Income
                        purchase, // COGS
                        directExpense, // Operating Expense
                        indirectExpense, // Other Expense
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

                        const colors = [
                            '#22d3ee',
                            '#a78bfa',
                            '#fbbf24',
                            '#f472b6',
                            isProfit ? '#22c55e' : '#ef4444'
                        ];

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
                    borderColor: 'rgba(0,0,0,0.15)',
                    borderWidth: 1.5,
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
                    }
                }
            }
        });


        //let bsData = JSON.parse('{!! json_encode($bsData ?? []) !!}');

        // let rows = bsData.rows || [];
        // Assets (DR → make positive)
        let assets = Math.abs(Number({{ $assets ?? 0 }}));
        // Liabilities
        let liabilities = Number({{ $liabs }});
        // Equity (Capital)
        let equity = Number({{ $equity }});
        // Total for center
        let totalAssets = assets;
        
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

                        const colors = ['#22d3ee', '#fbbf24', '#a78bfa'];

                        return colors.map(color => {
                            const gradient = c.createLinearGradient(0, chartArea.top, 0,
                                chartArea.bottom);

                            gradient.addColorStop(0, color + 'FF');
                            gradient.addColorStop(0.4, color + 'AA');
                            gradient.addColorStop(0.7, color + '55');
                            gradient.addColorStop(1, color + '10');

                            return gradient;
                        });
                    },
                    borderColor: 'rgba(0,0,0,0.15)',
                    borderWidth: 1.5,
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
                    }
                }
            }
        });

        let plLabels = [
            'Income',
            'COGS',
            'Op. Exp',
            'Other Exp',
            isProfit ? 'Profit' : 'Loss' // 🔥 ADD
        ];

        let plValues = [
            sales + indirectIncome,
            purchase,
            directExpense,
            indirectExpense,
            Math.abs(net)
        ];

        let plColors = [
            '#22d3ee',
            '#a78bfa',
            '#fbbf24',
            '#f472b6',
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

        let chartData = {
            sales: @json($charts[0]['in'] ?? []),
            purchase: @json($charts[0]['out'] ?? []),

            direct_income: @json($charts[1]['in'] ?? []),
            indirect_income: @json($charts[2]['in'] ?? []),

            direct_expense: @json($charts[1]['out'] ?? []),
            indirect_expense: @json($charts[2]['out'] ?? [])
        };

        let months = @json($charts[0]['months'] ?? []);
        const textColor = isDarkMode() ? '#e5e7eb' : '#000000'; // white / black
        const gridColor = isDarkMode() ? '#000000' : '#e5e7eb'; // dark / light grid
        
        function isDarkMode() {
            return document.documentElement.classList.contains('dark');
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
            // renderChartFor(activeType, selectedMetric, selectedCompare);
            renderDynamicChart();
        });

        function handleRangeChange(value) {

            let from = '';
            let to = '';

            if (value === 'current_year') {
                from = "{{ $currStart->format('Y-m-d') }}";
                to   = "{{ $currEnd->format('Y-m-d') }}";
            }

            if (value === 'last_year') {
                from = "{{ $lastStart->format('Y-m-d') }}";
                to   = "{{ $lastEnd->format('Y-m-d') }}";
            }

            if (value === 'custom') {
                // don't auto set → user will pick manually
                return;
            }

            document.getElementById('fy_from').value = from;
            document.getElementById('fy_to').value = to;

            document.getElementById('graphForm').submit();
        }

        //let salesChart = null;
        // function handleTypeChange(type) {
            
        //     let labelMap = {
        //         sales: 'Sales',
        //         purchase: 'Purchase',
        //         direct_income: 'Direct Income',
        //         indirect_income: 'Indirect Income',
        //         direct_expense: 'Direct Expense',
        //         indirect_expense: 'Indirect Expense'
        //     };

        //     let colorMap = {
        //         sales: '#22d3ee',
        //         purchase: '#050505',
        //         direct_income: '#34d399',
        //         indirect_income: '#a78bfa',
        //         direct_expense: '#f472b6',
        //         indirect_expense: '#fb7185'
        //     };
            
        //     // ✅ SAFE DEFAULT
        //     if (!colorMap[type]) {
        //         console.warn('Invalid type:', type);
        //         type = 'sales'; // fallback
        //     }
        //     alert(type);
        //     const color = colorMap[type];

        //     salesChart.data.datasets[0].data = chartData[type] || [];

        //     salesChart.data.datasets[0].backgroundColor = function(ctx) {
        //         const chart = ctx.chart;
        //         const { ctx: canvas, chartArea } = chart;

        //         if (!chartArea) return color;

        //         const gradient = canvas.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);

        //         gradient.addColorStop(0, color + 'FF');
        //         gradient.addColorStop(0.3, color + '99');
        //         gradient.addColorStop(1, color + '22');

        //         return gradient;
        //     };

        //     salesChart.update();
        // }
        
        // function handleTypeChange(type) {
        //     let labelMap = {
        //         sales: 'Sales',
        //         purchase: 'Purchase',
        //         direct_income: 'Direct Income',
        //         indirect_income: 'Indirect Income',
        //         direct_expense: 'Direct Expense',
        //         indirect_expense: 'Indirect Expense'
        //     };

        //     let colorMap = {
        //         sales: '#22d3ee',
        //         purchase: '#fbbf24',
        //         direct_income: '#34d399',
        //         indirect_income: '#a78bfa',
        //         direct_expense: '#f472b6',
        //         indirect_expense: '#fb7185'
        //     };

        //     // 🔥 UPDATE CHART (no destroy needed)
        //     salesChart.data.datasets[0].data = chartData[type];
        //     salesChart.data.datasets[0].label = labelMap[type];
        //     salesChart.data.datasets[0].backgroundColor = function(ctx) {
        //         const chart = ctx.chart;
        //         const {
        //             ctx: canvas,
        //             chartArea
        //         } = chart;
        //         if (!chartArea) return colorMap[type];

        //         const gradient = canvas.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);

        //         const color = colorMap[type];

        //         gradient.addColorStop(0, color + 'FF'); // strong top
        //         gradient.addColorStop(0.3, color + '99'); // glow mid
        //         gradient.addColorStop(0.6, color + '55'); // smooth fade
        //         gradient.addColorStop(1, color + '22'); // visible base

        //         return gradient;
        //     };

        //     salesChart.data.datasets[0].borderColor = colorMap[type];
        //     salesChart.data.datasets[0].borderWidth = 1.5;
        //     salesChart.data.datasets[0].borderRadius = 4;

        //     salesChart.update();


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
                    data = chartMap.salesPurchase?.out  || [];
                    title = 'Sales';
                    color = '#22d3ee';
                    break;

                case 'purchase':
                    labels = chartMap.salesPurchase?.months || [];
                    data = chartMap.salesPurchase?.in  || [];
                    title = 'Purchase';
                    color = '#a78bfa';
                    break;

                case 'direct_income':
                    labels = chartMap.receiptPayment?.months  || [];
                    data = chartMap.receiptPayment?.in  || [];
                    title = 'Direct Income';
                    color = '#34d399';
                    break;

                case 'indirect_income':
                    labels = chartMap.cashBank?.months  || [];
                    data = chartMap.cashBank?.in  || [];
                    title = 'Indirect Income';
                    color = '#fbbf24';
                    break;

                case 'direct_expense':
                    labels = chartMap.receiptPayment?.months  || [];
                    data = chartMap.receiptPayment?.out  || [];
                    title = 'Direct Expense';
                    color = '#f472b6';
                    break;

                case 'indirect_expense':
                    labels = chartMap.cashBank?.months  || [];
                    data = chartMap.cashBank?.out  || [];
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
                    labels: labels,
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

        //let salesChart = null;

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
                    data = c.in || [];   // 🔥 same source (if no separate data)
                    label = 'Direct Income';
                    color = '#34d399';
                    break;

                case 'indirect_income':
                    data = c.in || [];
                    label = 'Indirect Income';
                    color = '#fbbf24';
                    break;

                case 'direct_expense':
                    data = c.out || [];
                    label = 'Direct Expense';
                    color = '#f472b6';
                    break;

                case 'indirect_expense':
                    data = c.out || [];
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
                        label: 'Sales',
                        data: chartData.sales,
                        backgroundColor: (ctx) => {
                            const chart = ctx.chart;
                            const {
                                ctx: canvas
                            } = chart;

                            const gradient = canvas.createLinearGradient(0, 0, 0, 300);
                            gradient.addColorStop(0, 'rgba(34,211,238,0.9)');
                            gradient.addColorStop(0.5, 'rgba(34,211,238,0.5)');
                            gradient.addColorStop(1, 'rgba(34,211,238,0.1)');

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
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

// function metricDropdown() {

//     return {
//         open: false,
//         // selected: 'sales',
//         selected:'Sales Accounts',
//         options: {},
//         chartOptions: {
//             // sales: 'Sales',
//             // purchase: 'Purchase',
//             // receipt: 'Receipt',
//             // payment: 'Payment',
//             // cash: 'Cash',
//             // bank: 'Bank',
//             // creditor: 'Creditors',
//             // debtor: 'Debtors',
//             'Sales Accounts': 'Sales',
//             'Purchase Accounts': 'Purchase',
//             'Rcpt': 'Receipt',
//             'Pymt': 'Payment',
//             'Cash-in-Hand': 'Cash',
//             'Bank Accounts': 'Bank',
//             'Sundry Debtors': 'Sundry Debtors',
//             'Sundry Creditors': 'Sundry Creditors'
//         },

//         advancedOptions: {
//             sales_purchase: 'Sales vs Purchase',
//             receipt_payment: 'Receipt vs Payment',
//             creditors_debtors: 'Creditors vs Debtors',
//             cash_bank: 'Cash & Bank'
//         },

//         init() {

//             window.metricDropdownInstance = this;

//             this.options = this.chartOptions;

//             this.$watch('selected', value => {

//                 currentMetric = value;

//                 if (!isChartInitialized) return;

//                 renderChartFor(
//                     activeType,
//                     value,
//                     currentCompare
//                 );
//             });
//         },

//         updateView(view) {

//             console.log('UPDATING VIEW:', view);

//             if (view === 'advanced') {

//                 this.options = this.advancedOptions;

//                 this.selected = 'sales_purchase';

//             } else {

//                 this.options = this.chartOptions;

//                 this.selected = 'sales';
//             }
//         }
//     }
// }

function metricDropdown() {

    return {

        open:false,

        selected:'sales',

        options:{},

        init() {

            this.updateOptions();

            this.$watch(() =>
                document.getElementById('viewType').value,
                () => {
                    this.updateOptions();
                }
            );

            this.$watch('selected', value => {

                document.getElementById('metricSelect').value = value;

                if (!isChartInitialized) return;

                renderDynamicChart();
            });
        },

        updateOptions() {

            const view =
                document.getElementById('viewType').value;

            this.options = {};

            Object.entries(metricConfig[view])
                .forEach(([key,val]) => {

                    this.options[key] = val.label;
                });

            this.selected = Object.keys(this.options)[0];
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
    
    // renderChartFor(
    //     activeType,
    //     currentMetric,
    //     currentCompare
    // );
    renderDynamicChart();
}

function renderDynamicChart()
{
    const viewType =
        document.getElementById('viewType')?.value || 'chart';

    const metric =
        document.getElementById('metricSelect')?.value || 'sales';

    const compare =
        document.getElementById('compareSelect')?.value || 'none';

    const activeType =
        parseInt(document.getElementById('fy_type')?.value || 1);

    // =========================
    // GET CURRENT CHART DATA
    // =========================
    //const chartData = window.chartDataMap?.[activeType];
    const chartData = getChartDataByMetric(activeType, metric);

    if (!chartData)
    {
        console.error('Chart data missing for type:', activeType);
        return;
    }

    // =========================
    // DESTROY OLD CHART
    // =========================
    if (window.mainChart instanceof Chart)
    {
        window.mainChart.destroy();
    }

    const ctx =
        document.getElementById('mainChart').getContext('2d');

    // =========================
    // COMMON LABELS
    // =========================
    const labels =
        chartData.months || [];

    let datasets = [];
    let chartType = 'bar';

    // =========================================================
    // NORMAL CHART VIEW
    // =========================================================
    if (viewType === 'chart')
    {
        chartType = 'bar';

        // =====================================================
        // SALES
        // =====================================================
        if (metric === 'sales')
        {
            let running = 0;

            const cumulative =
                (chartData.in || []).map(v => {
                    running += Number(v || 0);
                    return running;
                });

            datasets = [

                {
                    label: 'Sales (Current Month)',
                    data: chartData.in || [],
                    backgroundColor: '#22d3ee',
                    borderColor: '#22d3ee',
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 26
                },

                {
                    label: 'Sales (Cumulative)',
                    data: cumulative,
                    backgroundColor: '#a78bfa',
                    borderColor: '#a78bfa',
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 26
                }
            ];
        }

        // =====================================================
        // PURCHASE
        // =====================================================
        else if (metric === 'purchase')
        {
            let running = 0;

            const cumulative =
                (chartData.out || []).map(v => {
                    running += Number(v || 0);
                    return running;
                });

            datasets = [

                {
                    label: 'Purchase (Current Month)',
                    data: chartData.out || [],
                    backgroundColor: '#f59e0b',
                    borderColor: '#f59e0b',
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 26
                },

                {
                    label: 'Purchase (Cumulative)',
                    data: cumulative,
                    backgroundColor: '#ef4444',
                    borderColor: '#ef4444',
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 26
                }
            ];
        }

        // =====================================================
        // RECEIPT
        // =====================================================
        else if (metric === 'receipt')
        {
            let running = 0;

            const cumulative =
                (chartData.in || []).map(v => {
                    running += Number(v || 0);
                    return running;
                });

            datasets = [

                {
                    label: 'Receipt (Current Month)',
                    data: chartData.in || [],
                    backgroundColor: '#10b981',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 26
                },

                {
                    label: 'Receipt (Cumulative)',
                    data: cumulative,
                    backgroundColor: '#06b6d4',
                    borderColor: '#06b6d4',
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 26
                }
            ];
        }

        // =====================================================
        // PAYMENT
        // =====================================================
        else if (metric === 'payment')
        {
            let running = 0;

            const cumulative =
                (chartData.out || []).map(v => {
                    running += Number(v || 0);
                    return running;
                });

            datasets = [

                {
                    label: 'Payment (Current Month)',
                    data: chartData.out || [],
                    backgroundColor: '#fb7185',
                    borderColor: '#fb7185',
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 26
                },

                {
                    label: 'Payment (Cumulative)',
                    data: cumulative,
                    backgroundColor: '#f43f5e',
                    borderColor: '#f43f5e',
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 26
                }
            ];
        }

        // =====================================================
        // CASH
        // =====================================================
        else if (metric === 'cash')
        {
            datasets = [

                {
                    label: 'Cash',
                    data: chartData.closingBalance || [],
                    backgroundColor: '#22d3ee',
                    borderColor: '#22d3ee',
                    borderWidth: 1,
                    borderRadius: 12,
                    barThickness: 44
                }
            ];
        }

        // =====================================================
        // BANK
        // =====================================================
        else if (metric === 'bank')
        {
            datasets = [

                {
                    label: 'Bank',
                    data: chartData.closingBalance || [],
                    backgroundColor: '#60a5fa',
                    borderColor: '#60a5fa',
                    borderWidth: 1,
                    borderRadius: 12,
                    barThickness: 44
                }
            ];
        }

        // =====================================================
        // SUNDRY DEBTOR
        // =====================================================
        else if (metric === 'debtor')
        {
            datasets = [

                {
                    label: 'Sundry Debtor',
                    data: chartData.closingBalance || [],
                    backgroundColor: '#f59e0b',
                    borderColor: '#f59e0b',
                    borderWidth: 1,
                    borderRadius: 12,
                    barThickness: 44
                }
            ];
        }

        // =====================================================
        // SUNDRY CREDITOR
        // =====================================================
        else if (metric === 'creditor')
        {
            datasets = [

                {
                    label: 'Sundry Creditor',
                    data: chartData.closingBalance || [],
                    backgroundColor: '#ef4444',
                    borderColor: '#ef4444',
                    borderWidth: 1,
                    borderRadius: 12,
                    barThickness: 44
                }
            ];
        }
    }

    // =========================================================
    // ADVANCED VIEW
    // =========================================================
    else
    {
        chartType = 'line';

        const currentIn =
            chartData.in || [];

        const currentOut =
            chartData.out || [];

        datasets = [

            {
                label: 'Current In',
                data: currentIn,
                borderColor: '#22d3ee',
                backgroundColor: '#22d3ee',
                tension: 0.4,
                fill: false
            },

            {
                label: 'Current Out',
                data: currentOut,
                borderColor: '#f43f5e',
                backgroundColor: '#f43f5e',
                tension: 0.4,
                fill: false
            }
        ];

        // =========================================
        // PREVIOUS MONTH
        // =========================================
        if (compare === 'prev-month')
        {
            datasets.push({

                label: 'Previous Month In',
                data: chartData.prevMonthIn || [],
                borderColor: '#a78bfa',
                backgroundColor: '#a78bfa',
                borderDash: [6, 6],
                tension: 0.4,
                fill: false
            });

            datasets.push({

                label: 'Previous Month Out',
                data: chartData.prevMonthOut || [],
                borderColor: '#facc15',
                backgroundColor: '#facc15',
                borderDash: [6, 6],
                tension: 0.4,
                fill: false
            });
        }

        // =========================================
        // PREVIOUS QUARTER
        // =========================================
        else if (compare === 'prev-quarter')
        {
            datasets.push({

                label: 'Previous Quarter In',
                data: chartData.prevQuarterIn || [],
                borderColor: '#8b5cf6',
                backgroundColor: '#8b5cf6',
                borderDash: [6, 6],
                tension: 0.4,
                fill: false
            });

            datasets.push({

                label: 'Previous Quarter Out',
                data: chartData.prevQuarterOut || [],
                borderColor: '#e879f9',
                backgroundColor: '#e879f9',
                borderDash: [6, 6],
                tension: 0.4,
                fill: false
            });
        }

        // =========================================
        // PREVIOUS YEAR
        // =========================================
        else if (compare === 'prev-year')
        {
            datasets.push({

                label: 'Previous Year In',
                data: chartData.prevYearIn || [],
                borderColor: '#34d399',
                backgroundColor: '#34d399',
                borderDash: [6, 6],
                tension: 0.4,
                fill: false
            });

            datasets.push({

                label: 'Previous Year Out',
                data: chartData.prevYearOut || [],
                borderColor: '#f97316',
                backgroundColor: '#f97316',
                borderDash: [6, 6],
                tension: 0.4,
                fill: false
            });
        }
    }

    // =========================================================
    // RENDER CHART
    // =========================================================
    window.mainChart = new Chart(ctx, {

        type: chartType,

        data: {

            labels: labels,

            datasets: datasets
        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            interaction: {
                mode: 'index',
                intersect: false
            },

            plugins: {

                legend: {

                    labels: {
                        color: '#ffffff'
                    }
                }
            },

            scales: {

                x: {

                    ticks: {
                        color: '#cbd5e1'
                    },

                    grid: {
                        color: 'rgba(255,255,255,0.05)'
                    }
                },

                y: {

                    beginAtZero: true,

                    ticks: {
                        color: '#cbd5e1'
                    },

                    grid: {
                        color: 'rgba(255,255,255,0.05)'
                    }
                }
            }
        }
    });
}

function buildSingleChartDataset(metric, data)
{
    const metricMap = {

        sales: {
            label: 'Sales',
            values: [...data.in],
            cumulative: true,
            color: '#22d3ee'
        },

        purchase: {
            label: 'Purchase',
            values: [...data.out],
            cumulative: true,
            color: '#a78bfa'
        },

        receipt: {
            label: 'Receipt',
            values: [...data.in],
            cumulative: true,
            color: '#34d399'
        },

        payment: {
            label: 'Payment',
            values: [...data.out],
            cumulative: true,
            color: '#f87171'
        },

        cash: {
            label: 'Cash',
            values: [...data.closingBalance],
            cumulative: false,
            color: '#60a5fa'
        },

        bank: {
            label: 'Bank',
            values: [...data.closingBalance],
            cumulative: false,
            color: '#3b82f6'
        },

        debtor: {
            label: 'Sundry Debtor',
            values: [...data.closingBalance],
            cumulative: false,
            color: '#f59e0b'
        },

        creditor: {
            label: 'Sundry Creditor',
            values: [...data.closingBalance],
            cumulative: false,
            color: '#ef4444'
        }
    };

    const config = metricMap[metric];

    if (!config) return [];

    const datasets = [];

    // SIMPLE BAR
    if (!config.cumulative)
    {
        datasets.push({

            label: config.label,
            data: config.values,
            backgroundColor: config.color,
            borderColor: config.color,
            borderWidth: 1,
            borderRadius: 6
        });

        return datasets;
    }

    // CUMULATIVE BAR
    let running = 0;

    const cumulative = config.values.map(v => {
        running += Number(v || 0);
        return running;
    });

    datasets.push({

        label: `${config.label} (Current Month)`,
        data: config.values,
        backgroundColor: config.color,
        borderColor: config.color,
        borderWidth: 1,
        borderRadius: 6
    });

    datasets.push({

        label: `${config.label} (Cumulative)`,
        data: cumulative,
        backgroundColor: '#a78bfa',
        borderColor: '#a78bfa',
        borderWidth: 1,
        borderRadius: 6
    });

    return datasets;
}

function renderChart(chartType, labels, datasets)
{
    const canvas =
        document.getElementById('mainChart');

    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    if (window.mainChartObj) {
        window.mainChartObj.destroy();
    }

    window.mainChartObj = new Chart(ctx, {

        type: chartType,

        data: {
            labels: labels,
            datasets: datasets
        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            interaction: {
                mode: 'index',
                intersect: false
            },

            plugins: {

                legend: {
                    display: true,
                    labels: {
                        color: '#cbd5e1'
                    }
                },

                tooltip: {
                    callbacks: {
                        label: function(context) {

                            return context.dataset.label
                                + ': '
                                + new Intl.NumberFormat('en-IN')
                                    .format(context.raw);
                        }
                    }
                }
            },

            scales: {

                x: {
                    ticks: {
                        color: '#94a3b8'
                    },
                    grid: {
                        color: 'rgba(255,255,255,0.05)'
                    }
                },

                y: {
                    ticks: {
                        color: '#94a3b8',

                        callback: function(value) {

                            return new Intl.NumberFormat('en-IN')
                                .format(value);
                        }
                    },

                    grid: {
                        color: 'rgba(255,255,255,0.05)'
                    }
                }
            }
        }
    });
}

function buildAdvancedDatasets(
    config,
    data,
    compare
) {

    let datasets = [];

    datasets.push({
        label:'Current In',
        data:data.in
    });

    datasets.push({
        label:'Current Out',
        data:data.out
    });

    if(compare === 'prev-month') {

        datasets.push({
            label:'Previous Month In',
            data:data.prevMonthIn
        });

        datasets.push({
            label:'Previous Month Out',
            data:data.prevMonthOut
        });
    }

    if(compare === 'prev-quarter') {

        datasets.push({
            label:'Previous Quarter In',
            data:data.prevQuarterIn
        });

        datasets.push({
            label:'Previous Quarter Out',
            data:data.prevQuarterOut
        });
    }

    if(compare === 'prev-year') {

        datasets.push({
            label:'Previous Year In',
            data:data.prevYearIn
        });

        datasets.push({
            label:'Previous Year Out',
            data:data.prevYearOut
        });
    }

    return datasets;
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
                borderColor: '#22d3ee',
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
                                color: '#d1d5db'
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
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#d1d5db'
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
                    labels: labels,
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
                                color: '#d1d5db'
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

        // function handleViewChange(view) {

        //     if (window.metricDropdownInstance) {

        //         // window.metricDropdownInstance.updateView(view);
        //         currentView = view;
        //         if (window.metricDropdownInstance) {
        //             window.metricDropdownInstance.updateView(view);
        //             currentMetric = window.metricDropdownInstance.selected;
        //         }

        //         currentCompare = document.getElementById('compareSelect')?.value || 'none';
                
        //         if (isChartInitialized) {
        //             renderChartFor(activeType, currentMetric, currentCompare);
        //         }
        //     }
        // }
        
</script>
<script>

window.addEventListener('load', () => {

    isChartInitialized = true;

    currentView = 'chart';
    currentMetric = 'sales';

    // renderChartFor(
    //     activeType,
    //     'Sales Accounts',
    //     'none'
    // );
    renderDynamicChart();
});

// document.addEventListener('DOMContentLoaded', () => {
//     setTimeout(() => {
//         isChartInitialized = true;
//         renderChartFor(
//             activeType,
//             'Sales Accounts',
//             'none'
//         );
//     }, 300);
// });

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
                    color: '#ffffff'
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
</script>