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
    //     $selectedGroups = array_slice($selectedGroups, 0, 8);
    //     session([$sessionKey => $selectedGroups]);
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
@endphp

@include('client_dashboard.topmenu')
<div class="container py-3">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="mt-4 sm:ml-auto flex flex-wrap items-center gap-2" role="tablist">

            <a href="{{ route('reports.balance_sheet') }}" style="padding-top: 0.40rem;"
                class="h-9 px-3 text-sm rounded-md border transition
               bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50">
                Balance Sheet
            </a>
            <a href="{{ route('reports.pl') }}" style="padding-top: 0.40rem;"
                class="h-9 px-3 text-sm rounded-md border transition
               bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50">
                Profit &amp; Loss A/C
            </a>

            <a href="{{ route('reports.ledger') }}" style="padding-top: 0.40rem;"
                class="h-9 px-3 text-sm rounded-md border transition
               bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50">
                All Ledger
            </a>
        </div>
    </div>
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Financial Dashboard</h1>
        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $labelFY ?? '' }}</div>
    </div>

    <form id="graphForm" method="GET" action="{{ route('home') }}"
        class="mt-4 bg-gray-100 dark:bg-gray-800/60 rounded-lg p-3">
        <input type="hidden" name="tab" value="financial">
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

            <!-- Group Selection Dropdown - FIXED ALPINE COMPONENT -->
            <!-- Group Selection Dropdown - SHOWS ALL GROUPS -->
            <div class="relative ml-auto" x-data="groupCustomizer({{ $selectedGroupsJson }}, {{ $allGroupIdsJson }}, {{ $defaultGroupIdsJson }})" x-init="init()">
                <button type="button" @click="open = !open"
                    class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <i class="fa-solid fa-layer-group text-gray-600 dark:text-gray-300"></i>
                    <span class="text-sm text-gray-700 dark:text-gray-200">Customize Groups</span>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-500 transition-transform duration-200"
                        :class="{ 'rotate-180': open }"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95" @click.away="open = false"
                    class="absolute right-0 z-50 w-80 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl"
                    style="display: none;">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-medium text-gray-900 dark:text-gray-100">Select Groups to Display</h3>
                            <button @click="open = false"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>
                        </div>

                        <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">
                            Choose groups in multiples of 4 (4, 8, 12, 16, or 20) to display on your dashboard.
                        </p>

                        <div class="max-h-64 overflow-y-auto pr-2 space-y-2">
                            @foreach ($allGroups as $index => $group)
                                @php
                                    $colorIndex = $index % count($colorMap);
                                    $colorKeys = array_keys($colorMap);
                                    $color = $colorKeys[$colorIndex];
                                    $isDefaultGroup = in_array($group->iGroupId, $defaultGroupIds ?? []);
                                    $isSelected = in_array((int) $group->iGroupId, $selectedGroups);
                                @endphp
                                <label
                                    class="flex items-center space-x-3 cursor-pointer p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                    :class="{
                                        'bg-blue-50 dark:bg-blue-900/20': selectedGroups.includes(
                                            {{ (int) $group->iGroupId }})
                                    }">
                                    <input type="checkbox" value="{{ (int) $group->iGroupId }}"
                                        x-model="selectedGroups"
                                        @change="console.log('Checkbox changed:', {{ (int) $group->iGroupId }}, selectedGroups)"
                                        :disabled="isGroupDisabled({{ (int) $group->iGroupId }})"
                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 flex-1">
                                        {{ $group->strGroupName }}
                                        @if ($isDefaultGroup)
                                            <span class="text-xs text-blue-600 dark:text-blue-400 ml-1">(Default)</span>
                                        @endif
                                    </span>
                                    <div class="ml-2 w-3 h-3 rounded-full {{ $colorMap[$color] }}"></div>
                                </label>
                            @endforeach
                        </div>

                        <!-- Multiple of 4 Status Section - ADDED HERE -->
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400"
                                    x-text="`${selectedGroups.length} groups selected`"></span>
                                <template x-if="getMultipleStatus().remainder !== 0">
                                    <span class="text-amber-600 dark:text-amber-400 font-medium"
                                        x-text="`Need ${4 - getMultipleStatus().remainder} more`"></span>
                                </template>
                                <template x-if="getMultipleStatus().remainder === 0">
                                    <span class="text-green-600 dark:text-green-400 font-medium">✓ Valid</span>
                                </template>
                            </div>

                            <!-- Progress indicator -->
                            <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                                    :style="`width: ${(selectedGroups.length % 4) * 25}%`"></div>
                            </div>
                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-center">
                                Select groups in multiples of 4
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
                            <span class="text-xs text-gray-500 dark:text-gray-400"
                                x-text="`${selectedGroups.length} of 20 groups selected`"></span>
                            <div class="flex gap-2">
                                <button type="button" @click="selectDefault()"
                                    class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    Default
                                </button>
                                <button type="button" @click="selectAll()"
                                    class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    Select All
                                </button>
                                <button type="button" @click="savePreferences()"
                                    :disabled="selectedGroups.length === 0 || selectedGroups.length % 4 !== 0"
                                    class="px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                    Apply
                                </button>
                            </div>
                        </div>

                        <!-- Warning messages -->
                        <div x-show="selectedGroups.length > 20" x-transition
                            class="mt-2 p-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded">
                            <p class="text-xs text-red-600 dark:text-red-400">
                                <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                                Maximum 20 groups allowed. Please deselect some groups.
                            </p>
                        </div>

                        <!-- Multiple of 4 warning -->
                        <div x-show="selectedGroups.length > 0 && selectedGroups.length % 4 !== 0" x-transition
                            class="mt-2 p-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded">
                            <p class="text-xs text-amber-600 dark:text-amber-400">
                                <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                                Please select groups in multiples of 4 (currently: <span
                                    x-text="selectedGroups.length"></span>).
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
</div>
</form>

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
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Accounts Summary</h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ count($allGroupCards) }} accounts</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="cardsContainer">
            @foreach ($allGroupCards as $card)
                <form method="GET" action="{{ route('reports.ledger') }}" class="card-form">
                    <button type="submit"
                        class="group block w-full text-left focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-xl card-hover">
                        <div
                            class="relative bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[92px] transition-all duration-300 hover:shadow-md">
                            <div class="absolute inset-y-0 left-0 w-1.5 {{ $leftBar($card['accent']) }}"></div>

                            <div class="p-4 pl-6">
                                <div class="flex items-start justify-between">
                                    <div class="pr-3 flex-1">
                                        <div
                                            class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400 truncate">
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

<div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <!-- Tabs on the left -->
    <div class="flex flex-wrap items-center gap-2" role="tablist">
        @foreach ($tabLabels as $t => $label)
            <button type="button" data-type="{{ $t }}"
                class="tab-button h-9 px-3 text-sm rounded-md border transition-all duration-200
              {{ $active === $t
                  ? 'bg-blue-600 text-white border-blue-600 hover:bg-blue-700 hover:border-blue-700 shadow-sm'
                  : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-300 dark:hover:border-blue-700 hover:text-blue-700 dark:hover:text-blue-300' }}">
                {{ $label }}
            </button>
        @endforeach
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


<div class="mt-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
    <div class="flex flex-wrap items-center justify-between mb-3 gap-2">


        <div class="text-xs text-gray-700 dark:text-gray-300">
            In: <strong id="totIn" class="text-sky-700 dark:text-sky-300">0.00</strong>
            &nbsp;|&nbsp;
            Out: <strong id="totOut" class="text-fuchsia-600 dark:text-fuchsia-400">0.00</strong>
        </div>
    </div>
    <div class="h-80 w-full">
        <canvas id="mainChart" class="h-full w-full"></canvas>
    </div>
</div>

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
                        const response = await fetch(
                            '{{ route('dashboard.save-card-preferences') }}', {
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
                            this.open = false;

                            // Update the selectedGroups with the validated ones
                            this.selectedGroups = validGroups;

                            // Reload after a short delay to show the notification
                            setTimeout(() => {
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const charts = @json($charts ?? []);
    let activeType = {{ (int) ($activeType ?? 1) }};
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
            colorCurrent = '#3b82f6'; // Light Blue
            colorCumulative = '#1e40af'; // Dark Blue
        } else if (metric === 'Purchase Accounts' && c.out && c.out.length > 0) {
            currentData = c.out.map(v => Number(v) || 0);
            cumulativeData = calculateCumulativeData(currentData);
            labelName = 'Purchase';
            colorCurrent = '#f59e0b'; // Light Orange
            colorCumulative = '#d97706'; // Dark Orange
        } else if (metric === 'Sundry Debtors' && c.in && c.in.length > 0) {
            currentData = c.in.map(v => Number(v) || 0);
            cumulativeData = calculateCumulativeData(currentData);
            labelName = 'Debtors';
            colorCurrent = '#10b981'; // Light Green
            colorCumulative = '#047857'; // Dark Green
        } else if (metric === 'Sundry Creditors' && c.out && c.out.length > 0) {
            currentData = c.out.map(v => Number(v) || 0);
            cumulativeData = calculateCumulativeData(currentData);
            labelName = 'Creditors';
            colorCurrent = '#8b5cf6'; // Light Purple
            colorCumulative = '#7c3aed'; // Dark Purple
        } else if (metric === 'Rcpt' && c.in && c.in.length > 0) {
            currentData = c.in.map(v => Number(v) || 0);
            cumulativeData = calculateCumulativeData(currentData);
            labelName = 'Receipts';
            colorCurrent = '#06b6d4'; // Light Cyan
            colorCumulative = '#0891b2'; // Dark Cyan
        } else if (metric === 'Pymt' && c.out && c.out.length > 0) {
            currentData = c.out.map(v => Number(v) || 0);
            cumulativeData = calculateCumulativeData(currentData);
            labelName = 'Payments';
            colorCurrent = '#ef4444'; // Light Red
            colorCumulative = '#dc2626'; // Dark Red
        } else if (metric === 'Cash-in-Hand') {
            // For Cash, show both in and out
            if (c.in && c.in.length > 0) {
                currentData = c.in.map(v => Number(v) || 0);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Cash In';
                colorCurrent = '#16a34a'; // Light Green
                colorCumulative = '#15803d'; // Dark Green
            }
        } else if (metric === 'Bank Accounts') {
            // For Bank, show both in and out
            if (c.in && c.in.length > 0) {
                currentData = c.in.map(v => Number(v) || 0);
                cumulativeData = calculateCumulativeData(currentData);
                labelName = 'Bank In';
                colorCurrent = '#0ea5e9'; // Light Blue
                colorCumulative = '#0369a1'; // Dark Blue
            }
        }

        // If we have data, create the two bars
        if (currentData.length > 0) {
            // Current Month Bar
            datasets.push({
                label: `${labelName} (Current Month)`,
                data: currentData,
                backgroundColor: colorCurrent,
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
            'Sales Accounts': ['#3b82f6', '#2563eb'],
            'Purchase Accounts': ['#f59e0b', '#b45309'],
            'Sundry Creditors': ['#8b5cf6', '#7c3aed'],
            'Sundry Debtors': ['#10b981', '#047857'],
            'Rcpt': ['#06b6d4', '#0891b2'],
            'Pymt': ['#dc2626', '#991b1b'],
            'Cash-in-Hand': ['#16a34a', '#166534'],
            'Bank Accounts': ['#0ea5e9', '#0369a1']
        };

        //const [colorIn, colorOut] = colors[metric] || ['#059669', '#dc2626'];
        const [colorIn, colorOut] = colors[metric] || ['#0ea5e9', '#d946ef']; // Default: Sky and Violet

        // Base datasets
        if (currentDataIn.length > 0 && currentLabelIn) {
            datasets.push({
                label: currentLabelIn,
                data: currentDataIn,
                borderColor: colorIn,
                backgroundColor: colorIn + '20',
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5,
                tension: 0.4,
                fill: false,
            });
        }

        if (currentDataOut.length > 0 && currentLabelOut) {
            datasets.push({
                label: currentLabelOut,
                data: currentDataOut,
                borderColor: colorOut,
                backgroundColor: colorOut + '20',
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5,
                tension: 0.4,
                fill: false,
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
                    backgroundColor: colorIn + '20',
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                    tension: 0.4,
                    fill: false,
                    borderDash: [5, 5],
                });
            }

            if (comparisonDataOut.length > 0 && currentLabelOut) {
                datasets.push({
                    label: `${currentLabelOut} (${comparisonLabel})`,
                    data: comparisonDataOut,
                    borderColor: colorOut,
                    backgroundColor: colorOut + '20',
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                    tension: 0.4,
                    fill: false,
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
            // options: {
            //     responsive: true,
            //     maintainAspectRatio: false,
            //     scales: {
            //         x: {
            //             grid: {
            //                 display: false
            //             },
            //             ticks: {
            //                 autoSkip: true,
            //                 maxRotation: 0
            //             }
            //         },
            //         y: {
            //             beginAtZero: true,
            //             ticks: {
            //                 callback: v => fmt(v)
            //             }
            //         }
            //     },
            //     plugins: {
            //         legend: {
            //             position: 'bottom',
            //             labels: {
            //                 usePointStyle: true,
            //                 padding: 20
            //             }
            //         },
            //         tooltip: {
            //             callbacks: {
            //                 label: ctx => `${ctx.dataset.label}: ₹ ${fmt(ctx.parsed.y)}`
            //             }
            //         }
            //     },
            //     interaction: {
            //         intersect: false,
            //         mode: 'index'
            //     }
            // }
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            title: (context) => {
                                // Force the title to be displayed in one line
                                const label = context[0].label;
                                return label.replace(/\s+/g,
                                    ' '); // Replace multiple spaces with single space
                            },
                            label: ctx => `${ctx.dataset.label}: ₹ ${fmt(ctx.parsed.y)}`
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
