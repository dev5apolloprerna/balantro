@php
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// FY helper defaults (current FY) if no range present
$tz = 'Asia/Kolkata';
$today = Carbon::today($tz);
if ($today->month < 4) {
    $currStart=Carbon::create($today->year - 1, 4, 1, 0, 0, 0, $tz);
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


    $totalAssets = 0;
    $totalCr     = 0;
    $liabs       = 0;
    $equity      = 0;
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
        $amt = (float)$r->decMainAmount;
        
        $totalCr += $amt;

        if (in_array($r->strGroupName, ['Capital Account', 'Profit & Loss A/c'])) {
            $equity += $amt;
        } else {
            $liabs += $amt;
        }

        if (str_contains($r->strGroupName, 'Current Liabilities')) {
            $currentLiabilities += $amt;
        }
        elseif (str_contains($r->strGroupName, 'Loans')) {
            $longTermLiabilities += $amt;
        }
        elseif (!in_array($r->strGroupName, ['Capital Account', 'Profit & Loss A/c'])) {
            $otherLiabilities += $amt;
        }
    }
    $assets = $totalAssets;
    @endphp

    <style>
        /* ===== 6 COLOR SYSTEM ===== */

        .color-0 {
            --card: #22d3ee;
        }

        /* cyan */
        .color-1 {
            --card: #a78bfa;
        }

        /* violet */
        .color-2 {
            --card: #34d399;
        }

        /* mint */
        .color-3 {
            --card: #fbbf24;
        }

        /* yellow */
        .color-4 {
            --card: #f472b6;
        }

        /* pink */
        .color-5 {
            --card: #60a5fa;
        }

        /* blue */

        /* Smooth base transition */
        .card-hover>div {
            transition: all 0.3s ease;
        }

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

        <form id="graphForm" method="GET" action="{{ route('home') }}"
            class="rounded-lg">
            <input type="hidden" name="tab" value="financial">
            <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4">

                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Financial Year
                    </label>
                    @php
                    $isLastFY = $fromVal === $lastStart->format('Y-m-d') && $toVal === $lastEnd->format('Y-m-d');
                    $isCurrentFY = $fromVal === $currStart->format('Y-m-d') && $toVal === $currEnd->format('Y-m-d');
                    @endphp
                    <select id="fyKey" name="fySel"
                        class="h-9 px-3 rounded-md border border-gray-300 dark:border-gray-700 
                        bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">

                        {{-- Current FY --}}
                        <option value="current"
                            data-from="{{ $currStart->format('Y-m-d') }}"
                            data-to="{{ $currEnd->format('Y-m-d') }}"
                            {{ $isCurrentFY ? 'selected' : '' }}>
                            FY {{ $currStart->format('Y') }}-{{ substr($currEnd->format('Y'), -2) }} (Current)
                        </option>

                        {{-- Last FY --}}
                        <option value="last"
                            data-from="{{ $lastStart->format('Y-m-d') }}"
                            data-to="{{ $lastEnd->format('Y-m-d') }}"
                            {{ $isLastFY ? 'selected' : '' }}>
                            FY {{ $lastStart->format('Y') }}-{{ substr($lastEnd->format('Y'), -2) }} (Previous)
                        </option>

                        {{-- Custom --}}
                        <!-- <option value="custom">Custom Range</option> -->
                    </select>
                    <input type="hidden" id="fy_from" name="from" value="{{ $fromVal }}">
                    <input type="hidden" id="fy_to" name="to" value="{{ $toVal }}">
                    <input type="hidden" id="fy_type" name="type" value="{{ (int) ($activeType ?? 1) }}">
                </div>

            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

        <div class="h-[320px] flex flex-col">
            <h3 class="text-gray-900 dark:text-white mb-3">Profit & Loss</h3>
            <div class="flex items-center justify-between h-full">
                <!-- LEFT: CHART -->
                <div style="width:180px; height:180px;">
                    <canvas id="plPie"></canvas>
                </div>
                <!-- RIGHT: LEGEND -->
                <div id="plLegend" class="text-gray-700 dark:text-white text-sm w-full space-y-2"></div>
            </div>
        </div>

        <div class="h-[320px] flex flex-col">
            <h3 class="text-gray-900 dark:text-white mb-3">Balance Sheet</h3>
            <div class="flex items-center justify-between h-full">
                <div style="width:180px; height:180px;">
                    <canvas id="bsPie"></canvas>
                </div>
                <div id="bsLegend" class="text-gray-700 dark:text-white text-sm w-full space-y-2"></div>
            </div>
        </div>

        <div class="h-[320px] bg-transparent flex flex-col">

            <div class="flex justify-between items-center mb-3">
                <h3 id="chartTitle" class="text-gray-900 dark:text-white">Sales</h3>

                <select id="typeSelect" class="bg-white dark:bg-black text-gray-900 dark:text-white 
                    border border-gray-300 dark:border-gray-600 
                    px-2 py-1 rounded text-sm">
                    <option value="sales">Sales</option>
                    <option value="purchase">Purchase</option>
                    <option value="direct_income">Direct Income</option>
                    <option value="indirect_income">Indirect Income</option>
                    <option value="direct_expense">Direct Expense</option>
                    <option value="indirect_expense">Indirect Expense</option>
                </select>
            </div>

            <div class="flex-1">
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
    <div class="mt-4 flex flex-wrap items-center justify-end gap-3">

        <div class="flex flex-wrap items-center gap-2">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    View:
                </label>

                <select id="viewType"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded-md text-sm">
                    <option value="1" {{ $active == 1 ? 'selected' : '' }}>Sales vs Purchase</option>
                    <option value="2" {{ $active == 2 ? 'selected' : '' }}>Creditors vs Debtors</option>
                    <option value="3" {{ $active == 3 ? 'selected' : '' }}>Receipt vs Payment</option>
                    <option value="4" {{ $active == 4 ? 'selected' : '' }}>Cash & Bank Flow</option>
                </select>
            </div>
        </div>
        <!-- Metric and Compare selectors on the right but aligned left within their container -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Metric Selector -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Metric:</label>
                <select id="metricSelect"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded-md text-sm">
                    <option value="">Select</option>
                    <option value="Sales Accounts">Sales</option>
                    <option value="Purchase Accounts">Purchase</option>
                    <option value="Sundry Creditors">Creditors</option>
                    <option value="Sundry Debtors">Debitors</option>
                    <option value="Rcpt">Receipts</option>
                    <option value="Pymt">Payment</option>
                    <option value="Cash-in-Hand">Cash</option>
                    <option value="Bank Accounts">Bank Flow</option>
                </select>
            </div>

            <!-- Comparison Selector -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Compare:</label>
                <select id="compareSelect"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded-md text-sm">
                    <option value="none">None</option>
                    <option value="prev-month">Previous Month</option>
                    <option value="prev-quarter">Previous Quarter</option>
                    <option value="prev-year">Previous Year</option>
                    {{-- <option value="budget">Budget / Target</option>
                <option value="forecast">Forecast</option>
                <option value="cashflow">Cash Flow</option>
                <option value="pl">Profit &amp; Loss</option> --}}
                </select>
            </div>
        </div>
    </div>


    <div class="mt-4 rounded-lg  p-4">
        <div class="flex flex-wrap items-center justify-between mb-3 gap-2">


            <div class="text-xs text-gray-700 dark:text-gray-300">
                In: <strong id="totIn" class="text-[#22d3ee] dark:text-[#22d3ee]">0.00</strong>
                &nbsp;|&nbsp;
                Out: <strong id="totOut" class="text-[#a78bfa] dark:text-[#a78bfa]">0.00</strong>
            </div>
        </div>
        <div class="h-80 w-full">
            <canvas id="mainChart" class="h-full w-full"></canvas>
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

    @if (!empty($allGroupCards) && count($allGroupCards) > 0)
    <div class="mt-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Accounts Summary
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ count($allGroupCards) }} accounts
                    </span>
                </h3>
            </div>

            <div class="relative"
                x-data="groupCustomizer({{ $selectedGroupsJson }}, {{ $allGroupIdsJson }}, {{ $defaultGroupIdsJson }})"
                x-init="init()">

                <!-- BUTTON -->
                <button type="button" @click="toggle()"
                    class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-black border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm hover:bg-gray-50">
                    <i class="fa-solid fa-layer-group"></i>
                    <span class="text-sm">Customize Groups</span>
                </button>

                <!-- DROPDOWN -->
                <div x-show="open"
                    x-transition
                    @click.away="closeDropdown()"
                    class="fixed right-6 top-[180px] z-50 w-80 
                    bg-white dark:bg-gray-800 border border-gray-200 
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
                                    'bg-blue-50 dark:bg-blue-900/20': selectedGroups.includes({{ (int) $group->iGroupId }})
                                }">

                            <input type="checkbox"
                                value="{{ (int) $group->iGroupId }}"
                                x-model="selectedGroups"
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
                    <div class="px-4 pt-3 border-t border-gray-200 dark:border-gray-600">
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
                    <div class="p-4 border-t border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
                        <div class="flex items-center justify-between">

                            <span class="text-xs text-gray-500"
                                x-text="`${selectedGroups.length} of 20 selected`"></span>

                            <div class="flex gap-2">

                                <button type="button" @click="selectDefault()"
                                    class="px-3 py-1.5 text-xs bg-gray-100 dark:bg-gray-700 rounded">
                                    Default
                                </button>

                                <button type="button" @click="selectAll()"
                                    class="px-3 py-1.5 text-xs bg-gray-100 dark:bg-gray-700 rounded">
                                    Select All
                                </button>

                                <button type="button" @click="savePreferences()"
                                    :disabled="selectedGroups.length === 0 || selectedGroups.length % 4 !== 0"
                                    class="px-3 py-1.5 text-xs text-white bg-indigo-600 rounded disabled:opacity-50">
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
                                <div
                                    class="h-9 w-9 md:h-10 md:w-10 rounded-full flex items-center justify-center {{ $chip($card['accent']) }} transition-colors group-hover:bg-opacity-80">
                                    <i class="{{ $card['icon'] }} text-sm md:text-base"></i>
                                </div>
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


    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> --}}

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

                        console.log('Group Customizer initialized with:', this.selectedGroups);
                        console.log('Available group IDs:', this.allGroupIds);

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
                            console.log('Deselected group:', groupIdInt, 'Current selection:', this
                                .selectedGroups);
                        } else {
                            // Add group if not at max limit
                            if (this.selectedGroups.length < this.SERVER_MAX_GROUPS) {
                                this.selectedGroups = [...this.selectedGroups, groupIdInt];
                                console.log('Selected group:', groupIdInt, 'Current selection:', this
                                    .selectedGroups);
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

                        console.log('Saving groups:', {
                            original: this.selectedGroups,
                            unique: uniqueGroups,
                            valid: validGroups,
                            allAvailable: this.allGroupIds
                        });

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
                            const response = await fetch("{{ route('dashboard.save-card-preferences') }}", {
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
                            console.log('Save response:', result);

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
            // This would typically make an API call to create demo groups
            // For now, just reload the page to trigger the demo groups in PHP
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
        }
    </script>

    <script>
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.addEventListener('click', function() {

                document.querySelectorAll('.tab-button').forEach(b => {

                    // Reset to default (inactive state)
                    b.classList.remove(
                        'bg-[#22d3ee]', 'text-white', 'border-[#22d3ee]', 'shadow-sm'
                    );

                    b.classList.add(
                        'bg-transparent', 'text-gray-700', 'border-gray-300'
                    );
                });

                // Apply active styles
                this.classList.remove(
                    'bg-white', 'text-gray-700', 'border-gray-300'
                );

                this.classList.add(
                    'bg-[#22d3ee]', 'text-white', 'border-[#22d3ee]', 'shadow-sm'
                );
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const charts = @json($charts ?? []);
        let activeType = {{ (int)($activeType ?? 1)}};
        let chart = null;

        console.log('Charts data:', charts);
        console.log('Active type:', activeType);

        // Format numbers with Indian formatting
        const fmt = v => new Intl.NumberFormat('en-IN', {
            maximumFractionDigits: 2
        }).format(Number(v) || 0);

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

        // Update tab active state
        function updateTabActiveState(type) {
            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                btn.classList.add('bg-white', 'dark:bg-gray-900', 'text-gray-700', 'dark:text-gray-300',
                    'border-gray-300', 'dark:border-gray-700');
            });

            // Add active class to current tab
            const activeTab = document.querySelector(`[data-type="${type}"]`);
            if (activeTab) {
                activeTab.classList.remove('bg-white', 'dark:bg-gray-900', 'text-gray-700', 'dark:text-gray-300',
                    'border-gray-300', 'dark:border-gray-700');
                activeTab.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
            }
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
        function renderCumulativeBarChart(c, labels, metric) {
            const datasets = [];

            console.log('Rendering CUMULATIVE BAR CHART for metric:', metric);

            // Get the data based on selected metric
            let currentData = [];
            let cumulativeData = [];
            let labelName = '';
            let colorCurrent = '#3b82f6';
            let colorCumulative = '#1e40af';

            if (metric === 'Sales Accounts' && c.in && c.in.length > 0) {
                currentData = c.in.map(v => Number(v) || 0);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Sales';
                colorCurrent = '#22d3ee'; // Light Blue
                colorCumulative = '#a78bfa'; // Dark Blue
            } else if (metric === 'Purchase Accounts' && c.out && c.out.length > 0) {
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
            } else if (metric === 'Rcpt' && c.in && c.in.length > 0) {
                currentData = c.in.map(v => Number(v) || 0);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Receipts';
                colorCurrent = '#22d3ee'; // Light Cyan
                colorCumulative = '#60a5fa'; // Dark Cyan
            } else if (metric === 'Pymt' && c.out && c.out.length > 0) {
                currentData = c.out.map(v => Number(v) || 0);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Payments';
                colorCurrent = '#22d3ee'; // Light Red
                colorCumulative = '#a78bfa'; // Dark Red
            } else if (metric === 'Cash-in-Hand') {
                // For Cash, show both in and out
                if (c.in && c.in.length > 0) {
                    currentData = c.in.map(v => Number(v) || 0);
                    cumulativeData = calculateCumulativeData(currentData);
                    labelName = 'Cash In';
                    colorCurrent = '#22d3ee'; // Light Green
                    colorCumulative = '#34d399'; // Dark Green
                }
            } else if (metric === 'Bank Accounts') {
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
                        const { ctx: c, chartArea } = chart;
                        if (!chartArea) return colorCurrent;

                        const gradient = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);

                        gradient.addColorStop(0, colorCurrent + 'FF'); // bright top
                        gradient.addColorStop(0.4, colorCurrent + '99');
                        gradient.addColorStop(1, colorCurrent + '22'); // glass fade

                        return gradient;
                    },
                    //borderRadius: 12,
                    //borderSkipped: false,
                    borderRadius: 12,
                    borderSkipped: false,
                    borderWidth: 1.5,
                    
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
                    backgroundColor: colorCumulative,
                    borderColor: colorCumulative,
                    borderWidth: 1,
                    borderRadius: 4,
                    categoryPercentage: 0.6,
                    barPercentage: 0.8
                });
            }

            return datasets;
        }

        const glowPlugin = {
            id: 'glow',
            beforeDatasetDraw(chart, args) {
                const { ctx } = chart;
                const dataset = chart.data.datasets[args.index];

                ctx.save();

                ctx.shadowColor = dataset.borderColor;
                ctx.shadowBlur = 40;

                // 🔥 IMPORTANT: apply glow on fill also
                ctx.globalCompositeOperation = 'lighter';
            },
            afterDatasetDraw(chart) {
                chart.ctx.restore();
            }
        };

        Chart.register(glowPlugin);

        // LINE CHART FUNCTION (for comparison view)
        function renderLineChart(c, labels, metric, compareWith) {
            

            const datasets = [];

            // Determine which data to show based on selected metric
            let currentDataIn = [];
            let currentDataOut = [];
            let currentLabelIn = '';
            let currentLabelOut = '';

            switch (metric) {
                case 'Sales Accounts':
                    currentDataIn = c.in || [];
                    currentDataOut = [];
                    currentLabelIn = 'Sales';
                    currentLabelOut = '';
                    break;
                case 'Purchase Accounts':
                    currentDataIn = [];
                    currentDataOut = c.out || [];
                    currentLabelIn = '';
                    currentLabelOut = 'Purchase';
                    break;
                case 'Sundry Creditors':
                    currentDataIn = [];
                    currentDataOut = c.out || [];
                    currentLabelIn = '';
                    currentLabelOut = 'Creditors';
                    break;
                case 'Sundry Debtors':
                    currentDataIn = c.in || [];
                    currentDataOut = [];
                    currentLabelIn = 'Debtors';
                    currentLabelOut = '';
                    break;
                case 'Rcpt':
                    currentDataIn = c.in || [];
                    currentDataOut = [];
                    currentLabelIn = 'Receipts';
                    currentLabelOut = '';
                    break;
                case 'Pymt':
                    currentDataIn = [];
                    currentDataOut = c.out || [];
                    currentLabelIn = '';
                    currentLabelOut = 'Payments';
                    break;
                case 'Cash-in-Hand':
                    currentDataIn = c.in || [];
                    currentDataOut = c.out || [];
                    currentLabelIn = 'Cash In';
                    currentLabelOut = 'Cash Out';
                    break;
                case 'Bank Accounts':
                    currentDataIn = c.in || [];
                    currentDataOut = c.out || [];
                    currentLabelIn = 'Bank In';
                    currentLabelOut = 'Bank Out';
                    break;
                default:
                    currentDataIn = c.in || [];
                    currentDataOut = c.out || [];
                    currentLabelIn = labels.in;
                    currentLabelOut = labels.out;
            }

            // Color palette
            const colors = {
                'Sales Accounts': ['#22d3ee', '#a78bfa'],
                'Purchase Accounts': ['#22d3ee', '#34d399'],
                'Sundry Debtors': ['#22d3ee', '#fbbf24'],
                'Sundry Creditors': ['#22d3ee', '#f472b6'],
                'Rcpt': ['#22d3ee', '#60a5fa'],
                'Pymt': ['#22d3ee', '#a78bfa'],
                'Cash-in-Hand': ['#22d3ee', '#34d399'],
                'Bank Accounts': ['#22d3ee', '#fbbf24']
            };

            //const [colorIn, colorOut] = colors[metric] || ['#059669', '#dc2626'];
            const [colorIn, colorOut] = colors[metric] || ['#22d3ee', '#a78bfa']; // Default: Sky and Violet
            const ctx = document.getElementById('mainChart').getContext('2d');

            // 🔵 Gradient for Sales
            const gradientIn = ctx.createLinearGradient(0, 0, 0, 300);
            gradientIn.addColorStop(0, colorIn + 'FF');   // top bright
            gradientIn.addColorStop(0.3, colorIn + '88'); // glow middle
            gradientIn.addColorStop(0.7, colorIn + '33'); // fade
            gradientIn.addColorStop(1, colorIn + '05');   // glass bottom

            // 🟣 Gradient for Purchase
            const gradientOut = ctx.createLinearGradient(0, 0, 0, 300);
            gradientOut.addColorStop(0, colorOut + 'FF');
            gradientOut.addColorStop(0.3, colorOut + '88');
            gradientOut.addColorStop(0.7, colorOut + '33');
            gradientOut.addColorStop(1, colorOut + '05');
            // Base datasets

            
            if (currentDataIn.length > 0 && currentLabelIn) {
                datasets.push({
                    label: currentLabelIn,
                    data: currentDataIn,
                    borderColor: colorIn,
                    //backgroundColor: colorIn + '15',
                    backgroundColor: gradientIn, // 🔥 IMPORTANT
                    // borderWidth: 2,
                    // pointRadius: 3,
                    // pointHoverRadius: 8,
                    borderWidth: 2.5,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    //fill: false,
                    // 🔥 GLASS EFFECT
                    // fill: true,
                    fill: {
                        target: 'origin',
                        above: gradientIn,
                    }
                });
            }

            if (currentDataOut.length > 0 && currentLabelOut) {
                datasets.push({
                    label: currentLabelOut,
                    data: currentDataOut,
                    borderColor: colorOut,
                    //backgroundColor: colorOut + '20',
                    backgroundColor: gradientOut, // 🔥 IMPORTANT
                    // borderWidth: 2,
                    // pointRadius: 3,
                    // pointHoverRadius: 8,
                    borderWidth: 2.5,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    //fill: true,
                    // 🔥 GLASS EFFECT
                    fill: {
                        target: 'origin',
                        above: gradientOut,
                    }
                });
            }

            // Comparison datasets (only for line chart when compare is selected)
            if (compareWith !== 'none' && compareWith !== 'cumulative') {
                let comparisonDataIn = [];
                let comparisonDataOut = [];
                let comparisonLabel = '';

                switch (compareWith) {
                    case 'prev-month':
                        comparisonDataIn = c.prevMonthIn || [];
                        comparisonDataOut = c.prevMonthOut || [];
                        comparisonLabel = 'Prev Month';
                        break;
                    case 'prev-quarter':
                        comparisonDataIn = c.prevQuarterIn || [];
                        comparisonDataOut = c.prevQuarterOut || [];
                        comparisonLabel = 'Prev Quarter';
                        break;
                    case 'prev-year':
                        comparisonDataIn = c.prevYearIn || [];
                        comparisonDataOut = c.prevYearOut || [];
                        comparisonLabel = 'Prev Year';
                        break;
                }

                if (comparisonDataIn.length > 0 && currentLabelIn) {
                    datasets.push({
                        label: `${currentLabelIn} (${comparisonLabel})`,
                        data: comparisonDataIn,
                        borderColor: colorIn,
                        //backgroundColor: colorIn + '20',
                        backgroundColor: gradientIn, // 🔥 IMPORTANT
                        // borderWidth: 2,
                        // pointRadius: 2,
                        // pointHoverRadius: 8,
                        borderWidth: 2.5,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        tension: 0.4,
                        //fill: true,
                        // 🔥 GLASS EFFECT
                        fill: {
                            target: 'origin',
                            above: gradientIn,
                        },
                        borderDash: [5, 5],
                    });
                }

                if (comparisonDataOut.length > 0 && currentLabelOut) {
                    datasets.push({
                        label: `${currentLabelOut} (${comparisonLabel})`,
                        data: comparisonDataOut,
                        borderColor: colorOut,
                        //  backgroundColor: colorOut + '20',
                        backgroundColor: gradientOut, // 🔥 IMPORTANT
                        // borderWidth: 2,
                        // pointRadius: 2,
                        // pointHoverRadius: 8,
                        borderWidth: 2.5,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        tension: 0.4,
                        //fill: true, 
                        fill: {
                            target: 'origin',
                            above: gradientIn,
                        },
                        borderDash: [5, 5],
                    });
                }
            }

            return datasets;
        }

        // Check if we should show cumulative bar chart
        function shouldShowCumulativeBar(metric, compareWith) {
            // Show cumulative bar chart when:
            // 1. A metric is selected AND
            // 2. Compare is 'none' (not selected)
            return metric && metric !== '' && compareWith === 'none';
        }

        // MAIN CHART RENDERING FUNCTION
        function renderChartFor(type, metric, compareWith) {
            console.log('Rendering chart with:', {
                type,
                metric,
                compareWith
            });

            const c = byKey[type] || charts[0];
            if (!c) {
                console.error('No chart data found for type:', type);
                // Set default labels
                updateBottomLabels({
                    in: 'In',
                    out: 'Out'
                });
                document.getElementById('totIn').textContent = '0.00';
                document.getElementById('totOut').textContent = '0.00';
                return;
            }

            const ctx = document.getElementById('mainChart').getContext('2d');

            // Destroy existing chart if it exists
            if (chart) {
                chart.destroy();
                chart = null;
            }

            const labels = getLabels(type, metric);

            // Update bottom labels dynamically
            updateBottomLabels(labels);

            let datasets = [];
            let chartType = 'line';

            // DECISION LOGIC: Bar chart for cumulative, Line chart for others
            if (shouldShowCumulativeBar(metric, compareWith)) {
                console.log('Rendering CUMULATIVE BAR CHART - Single metric with no comparison');
                chartType = 'bar';
                datasets = renderCumulativeBarChart(c, labels, metric);
            } else {
                console.log('Rendering LINE CHART - Multiple metrics or with comparison');
                chartType = 'line';
                datasets = renderLineChart(c, labels, metric, compareWith);
            }

            // If no datasets (no data), show empty state
            if (datasets.length === 0) {
                datasets = [{
                    label: 'No Data',
                    data: Array(c.months?.length || 12).fill(0),
                    borderColor: '#9ca3af',
                    backgroundColor: '#9ca3af20',
                    borderWidth: 1,
                    pointRadius: 0,
                    fill: false,
                }];
            }

            // Create the chart
            chart = new Chart(ctx, {
                type: chartType,
                data: {
                    labels: (c.months || []).map(String),
                    datasets: datasets
                },
                
                cutout: '70%',
                radius: '90%',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
                    //     x: {
                    //         grid: {
                    //             display: false
                    //         },
                    //         ticks: {
                    //             autoSkip: true,
                    //             maxRotation: 0
                    //         }
                    //     },
                    //     y: {
                    //         beginAtZero: true,
                    //         ticks: {
                    //             callback: v => fmt(v)
                    //         }
                    //     }
                    // },

                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#aaa' }
                        },
                        y: {
                            grid: { color: 'rgba(255,255,255,0.05)' },
                            ticks: { color: '#aaa' }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            align: 'center',
                            labels: {
                                usePointStyle: true,
                                // padding: 20,
                                boxWidth: 10,
                                boxHeight: 10,
                                textAlign: 'center',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            displayColors: true,
                            usePointStyle: true,

                            callbacks: {
                                title: (context) => {
                                    const label = context[0].label;
                                    return label.replace(/\s+/g, ' ');
                                },

                                label: ctx => `${ctx.dataset.label}: ₹ ${fmt(ctx.parsed.y)}`,

                                // 🔥 THIS IS THE MAIN FIX
                                labelColor: function(ctx) {
                                    return {
                                        borderColor: ctx.dataset.borderColor, // dark border
                                        backgroundColor: ctx.dataset.borderColor // filled with same color
                                    };
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // Update totals based on the actual data being displayed
            let totalIn = 0;
            let totalOut = 0;

            if (shouldShowCumulativeBar(metric, compareWith)) {
                // For cumulative bar chart, show sum of current month values
                if (metric === 'Sales Accounts' || metric === 'Sundry Debtors' || metric === 'Rcpt' ||
                    metric === 'Cash-in-Hand' || metric === 'Bank Accounts') {
                    totalIn = (c.in || []).reduce((sum, val) => sum + (Number(val) || 0), 0);
                } else if (metric === 'Purchase Accounts' || metric === 'Sundry Creditors' || metric === 'Pymt') {
                    totalOut = (c.out || []).reduce((sum, val) => sum + (Number(val) || 0), 0);
                }
            } else {
                // For line chart, show totals based on what's being displayed
                const currentDataIn = c.in ? c.in.map(v => Number(v) || 0) : [];
                const currentDataOut = c.out ? c.out.map(v => Number(v) || 0) : [];

                totalIn = currentDataIn.reduce((a, b) => a + b, 0);
                totalOut = currentDataOut.reduce((a, b) => a + b, 0);
            }

            // Update the totals in the dynamically created elements
            const totInElement = document.getElementById('totIn');
            const totOutElement = document.getElementById('totOut');

            if (totInElement) totInElement.textContent = fmt(totalIn);
            if (totOutElement) totOutElement.textContent = fmt(totalOut);

            console.log('Chart rendered successfully');
        }

        // Initialize chart and event listeners
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing chart...');

            updateTabActiveState(activeType);
            renderChartFor(activeType, '', 'none');

            // Set default selections
            document.getElementById('metricSelect').value = '';
            document.getElementById('compareSelect').value = 'none';

            // Event listeners
            document.getElementById('metricSelect').addEventListener('change', e => {
                const metric = e.target.value;
                const compareWith = document.getElementById('compareSelect').value;
                console.log('Metric changed to:', metric, 'Compare:', compareWith);
                renderChartFor(activeType, metric, compareWith);
            });

            document.getElementById('compareSelect').addEventListener('change', e => {
                const compareWith = e.target.value;
                const metric = document.getElementById('metricSelect').value;
                console.log('Compare changed to:', compareWith, 'Metric:', metric);
                renderChartFor(activeType, metric, compareWith);
            });

            // Tab button click handlers with reset functionality
            document.querySelectorAll('[data-type]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const type = Number(this.dataset.type);
                    console.log('Tab button clicked, changing to type:', type);

                    // RESET DROPDOWNS as per requirement
                    resetDropdowns();

                    // Update active type and UI
                    activeType = type;
                    updateTabActiveState(activeType);

                    // IMMEDIATELY rebuild the chart with default settings
                    console.log('Rebuilding chart immediately after tab click');
                    renderChartFor(type, '', 'none');
                });
            });

            console.log('Chart initialization complete');
        });
    </script>
    <script>
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
                    backgroundColor: [
                        'rgba(34,211,238,0.85)',
                        'rgba(167,139,250,0.85)',
                        'rgba(251,191,36,0.85)',
                        'rgba(244,114,182,0.85)',
                        isProfit ? 'rgba(34,197,94,0.85)' : 'rgba(239,68,68,0.85)'
                    ],
                    borderColor: 'rgba(255,255,255,0.15)',
                    borderWidth: 1.5,
                }]
            },
            
            cutout: '70%',
                radius: '90%',
            options: {
                responsive: true,
                maintainAspectRatio: false,
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

        let bsData = JSON.parse('{!! json_encode($bsData ?? []) !!}');

        let rows = bsData.rows || [];
        // Assets (DR → make positive)
        let assets = Math.abs(Number(bsData.totals?.assets || 0));
        // Liabilities
        let liabilities = Number(bsData.totals?.liabilities || 0);
        // Equity (Capital)
        let equity = Number(bsData.totals?.equity || 0);
        // Total for center
        let totalAssets = assets;
        console.log('Balance Sheet Data:', {
            assets,
            liabilities,
            equity
        });
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
                    backgroundColor: [
                        'rgba(34,211,238,0.85)',
                        'rgba(167,139,250,0.85)',
                        'rgba(251,191,36,0.85)',
                        'rgba(244,114,182,0.85)',
                        isProfit ? 'rgba(34,197,94,0.85)' : 'rgba(239,68,68,0.85)'
                    ],
                    borderColor: 'rgba(255,255,255,0.15)',
                    borderWidth: 1.5,
                }]
            },
            
            cutout: '70%',
                radius: '90%',
            options: {
                responsive: true,
                maintainAspectRatio: false,
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

                let value = Number(values[i] || 0);

                html += `
                    <div class="flex items-center justify-between gap-3">

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
                        <div class="text-right font-medium text-white whitespace-nowrap">
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
        let salesChart = new Chart(document.getElementById('salesBar'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Sales',
                    data: chartData.sales,
                    backgroundColor: (ctx) => {
                    const chart = ctx.chart;
                    const { ctx: canvas } = chart;

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
            
            cutout: '70%',
                radius: '90%',
            options: {
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
                            color: gridColor
                        },
                        ticks: {
                            color: textColor // ✅ FIX
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                           color: gridColor
                           
                        },
                        ticks: {
                            color: textColor, // ✅ FIX
                            callback: v => fmt(v)
                        }
                    }
                },
                plugins: {
                    // legend: {
                    //     labels: {
                    //         color: document.documentElement.classList.contains('dark') ? '#fff' : '#111'
                    //     }
                    // }
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: textColor, // ✅ FIX
                            usePointStyle: true,
                            boxWidth: 10
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        document.getElementById('typeSelect').addEventListener('change', function() {

            let type = this.value;

            let labelMap = {
                sales: 'Sales',
                purchase: 'Purchase',
                direct_income: 'Direct Income',
                indirect_income: 'Indirect Income',
                direct_expense: 'Direct Expense',
                indirect_expense: 'Indirect Expense'
            };

            let colorMap = {
                sales: '#22d3ee',
                purchase: '#fbbf24',
                direct_income: '#34d399',
                indirect_income: '#a78bfa',
                direct_expense: '#f472b6',
                indirect_expense: '#fb7185'
            };

            // 🔥 UPDATE CHART (no destroy needed)
            salesChart.data.datasets[0].data = chartData[type];
            salesChart.data.datasets[0].label = labelMap[type];
            salesChart.data.datasets[0].backgroundColor = colorMap[type];

            salesChart.update();
        });

        const typeSelect = document.getElementById('typeSelect');
        const chartTitle = document.getElementById('chartTitle');

        typeSelect.addEventListener('change', function() {

            const map = {
                sales: 'Sales',
                purchase: 'Purchase',
                direct_income: 'Direct Income',
                indirect_income: 'Indirect Income',
                direct_expense: 'Direct Expense',
                indirect_expense: 'Indirect Expense'
            };

            chartTitle.textContent = map[this.value] || 'Sales';

            // 🔥 ALSO update chart data here if needed
            updateSalesChart(this.value);
        });

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
            activeType = type;

            // 🔥 RESET DROPDOWNS (YOU MISSED THIS)
            document.getElementById('metricSelect').value = '';
            document.getElementById('compareSelect').value = 'none';

            // 🔥 UPDATE HIDDEN FIELD (IMPORTANT FOR FORM)
            const fyType = document.getElementById('fy_type');
            if (fyType) fyType.value = type;

            // 🔥 CALL CHART
            renderChartFor(activeType, '', 'none');
        });
    </script>