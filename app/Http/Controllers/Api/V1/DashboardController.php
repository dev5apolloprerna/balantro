<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserCardPreference;
use App\Models\Group;
use App\Services\ReportsService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DashboardController extends BaseApiController
{
    protected $reportsService;

    public function __construct(ReportsService $reportsService)
    {
        $this->reportsService = $reportsService;
    }

    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            if ($user->role != User::ROLES['client']) {
                return $this->error(__("response_message.dashboard.unauthorized_role"), 403);
            }

            $userId = (int) $user->id;
            $from = $request->input('from');
            $to = $request->input('to');

            // Get document summary
            $rows = DB::select('EXEC dbo.usp_GetClientDocumentSummary ?', [$userId]);
            $row = $rows[0] ?? (object) [];

            // Get groups with balances
            $allGroupsWithBalances = $this->reportsService->getAllGroupsWithBalances($userId, $from, $to);
            $allGroups = collect($allGroupsWithBalances);

            // Default financial groups
            $defaultGroupNames = [
                'Sales Accounts',
                'Purchase Accounts',
                'Sundry Creditors',
                'Sundry Debtors',
                'Cash-in-Hand',
                'Bank Accounts',
                'Direct Incomes',
                'Direct Expenses'
            ];

            // Get user card preferences - FIXED THE JSON DECODE ISSUE
            $preferences = UserCardPreference::where('user_id', $userId)
                ->where('party_id', $userId)
                ->first();

            if ($preferences && $preferences->selected_groups) {
                // Check if selected_groups is already an array
                if (is_array($preferences->selected_groups)) {
                    $selectedGroups = $preferences->selected_groups;
                } else {
                    // It's a string, so decode it
                    $selectedGroups = json_decode($preferences->selected_groups, true);
                }
                $selectedGroups = array_map('intval', $selectedGroups ?? []);
            } else {
                // Use default groups if no preferences
                $selectedGroups = $allGroups
                    ->whereIn('strGroupName', $defaultGroupNames)
                    ->pluck('iGroupId')
                    ->toArray();
            }

            // Validate selected groups exist
            $validSelectedGroups = [];
            foreach ($selectedGroups as $groupId) {
                if ($allGroups->contains('iGroupId', $groupId)) {
                    $validSelectedGroups[] = $groupId;
                }
            }

            // Fallback to default groups if none valid
            if (empty($validSelectedGroups)) {
                $validSelectedGroups = $allGroups
                    ->whereIn('strGroupName', $defaultGroupNames)
                    ->pluck('iGroupId')
                    ->toArray();
            }

            $selectedGroups = $validSelectedGroups;

            // Build group cards with balances
            $groupCards = $allGroups
                ->whereIn('iGroupId', $selectedGroups)
                ->map(function ($group) {
                    return [
                        'key' => 'group_' . $group->iGroupId,
                        'iGroupId' => (int)$group->iGroupId,
                        'value' => (float)($group->Closing ?? 0),
                        'name' => $group->strGroupName,
                        'label' => $group->strGroupName,
                        'accent' => $this->getAccentColor($group->strGroupName),
                        'icon' => $this->getGroupIcon($group->strGroupName),
                        'opening_balance' => (float)($group->Opening ?? 0),
                        'closing_balance' => (float)($group->Closing ?? 0)
                    ];
                })
                ->values()
                ->toArray();

            return $this->success(
                __("response_message.dashboard.dashboard_data"),
                [
                    'document_summary' => [
                        'uploaded_count'    => (int) ($row->uploaded_count    ?? 0),
                        'in_progress_count' => (int) ($row->in_progress_count ?? 0),
                        'completed_count'   => (int) ($row->completed_count   ?? 0),
                        'rejected_count'    => (int) ($row->rejected_count    ?? 0),
                        'accepted_count'    => (int) ($row->accepted_count    ?? 0),
                    ],
                    'group_cards' => $groupCards,
                    'available_groups' => $allGroups->map(function ($group) {
                        return [
                            'iGroupId' => (int)$group->iGroupId,
                            'strGroupName' => $group->strGroupName,
                            'Closing' => (float)($group->Closing ?? 0),
                            'Opening' => (float)($group->Opening ?? 0),
                        ];
                    })->values()->toArray(),
                    'selected_group_ids' => $selectedGroups
                ]
            );
        } catch (\Exception $e) {
            return $this->error(__("response_message.dashboard.dashboard_error"), 500, $e->getMessage());
        }
    }

    public function saveCardPreferences(Request $request)
    {
        try {
            $user = auth()->user();

            if ($user->role != User::ROLES['client']) {
                return $this->error(__("response_message.dashboard.unauthorized_role"), 403);
            }

            $validator = Validator::make($request->all(), [
                'selected_groups' => 'required|array',
                'selected_groups.*' => 'integer|exists:GroupMaster,iGroupId'
            ]);

            if ($validator->fails()) {
                return $this->error(__("response_message.validation_failed"), 422, $validator->errors());
            }

            // Ensure multiples of 4 for grid layout
            if (count($request->selected_groups) % 4 !== 0) {
                return $this->error(
                    __('response_message.dashboard.groups_multiple_of_four', ['count' => count($request->selected_groups)]),
                    422
                );
            }

            $selectedGroups = array_map('intval', $request->selected_groups);

            $preference = UserCardPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'party_id' => $user->id
                ],
                [
                    'selected_groups' => $selectedGroups
                ]
            );

            // Clear session cache
            $sessionKey = "user_{$user->id}_selected_groups";
            session()->forget($sessionKey);

            return $this->success(
                __("response_message.dashboard.preferences_saved"),
                [
                    'groups_count' => count($selectedGroups),
                    'selected_groups' => $selectedGroups
                ]
            );
        } catch (\Exception $e) {
            return $this->error(__("response_message.dashboard.preferences_error"), 500, $e->getMessage());
        }
    }

    public function dropdown_type_list(Request $request)
    {
        try {
            $user = auth()->user();

            if ($user->role != User::ROLES['client']) {
                return $this->error(__("response_message.dashboard.unauthorized_role"), 403);
            }

            $summary = [
                'chart_types' => [
                    ['key' => '1', 'value' => "Sales & Purchase"],
                    ['key' => '2', 'value' => "Creditors & Debtors"],
                    ['key' => '3', 'value' => "Receipt & Payment"],
                    ['key' => '4', 'value' => "Cash & Bank balance"]
                ],
                'metric_options' => [
                    ['key' => '', 'value' => "Select Metric", 'group' => 'All'],
                    ['key' => 'Sales Accounts', 'value' => "Sales", 'group' => 'Sales & Purchase'],
                    ['key' => 'Purchase Accounts', 'value' => "Purchase", 'group' => 'Sales & Purchase'],
                    ['key' => 'Sundry Debtors', 'value' => "Debtors", 'group' => 'Credit & Debit'],
                    ['key' => 'Sundry Creditors', 'value' => "Creditors", 'group' => 'Credit & Debit'],
                    ['key' => 'Rcpt', 'value' => "Receipts", 'group' => 'Receipt & Payment'],
                    ['key' => 'Pymt', 'value' => "Payments", 'group' => 'Receipt & Payment'],
                    ['key' => 'Cash-in-Hand', 'value' => "Cash", 'group' => 'Cash & Bank'],
                    ['key' => 'Bank Accounts', 'value' => "Bank Flow", 'group' => 'Cash & Bank']
                ],
                'comparison_options' => [
                    ['key' => 'none', 'value' => "No Comparison"],
                    ['key' => 'prev-month', 'value' => "Compare with Previous Month"],
                    ['key' => 'prev-quarter', 'value' => "Compare with Previous Quarter"],
                    ['key' => 'prev-year', 'value' => "Compare with Previous Year"]
                ]
            ];

            return $this->success(__("response_message.dashboard.dropdown_type_list"), $summary);
        } catch (\Exception $e) {
            return $this->error(__("response_message.dashboard.dropdown_type_error"), 500, $e->getMessage());
        }
    }

    public function financialGraphs(Request $request)
	{
		try {
			$user = auth()->user();

			if ($user->role != User::ROLES['client']) {
				return $this->error(__("response_message.dashboard.unauthorized_role"), 403);
			}

			$validator = Validator::make($request->all(), [
				'type' => 'required|in:1,2,3,4',
				'from' => 'nullable|date_format:Y-m-d',
				'to' => 'nullable|date_format:Y-m-d|after_or_equal:from',
				'start_date' => 'nullable|date_format:d-m-Y',
				'end_date' => 'nullable|date_format:d-m-Y|after_or_equal:start_date',
				'fySel' => 'nullable|string',
				'partyguid' => 'nullable|string',
				'metric' => 'nullable|string',
				'compare' => 'nullable|string'
			]);

			if ($validator->fails()) {
				return $this->error(__("response_message.validation_failed"), 422, $validator->errors());
			}

			$userId = (int) $user->id;
			$type = (int) $request->input('type', 1);
			$fySel = $request->input('fySel');
			$metric = $request->input('metric');
			$compare = $request->input('compare');

			// Handle date parameters - FIX THE DATE ISSUE
			$from = $request->input('from');
			$to = $request->input('to');

			// If mobile format provided, convert to database format
			if (!$from && $request->has('start_date')) {
				$startDate = $request->input('start_date');
				$from = \Carbon\Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d');
			} else {
				// Default to current financial year if no dates provided
				$currentYear = date('Y');
				$currentMonth = date('m');

				// Determine financial year (April to March)
				if ($currentMonth >= 4) {
					// April to December - current year to next year
					$from = $currentYear . '-04-01';
					$to = ($currentYear + 1) . '-03-31';
				} else {
					// January to March - previous year to current year
					$from = ($currentYear - 1) . '-04-01';
					$to = $currentYear . '-03-31';
				}
			}

			if (!$to && $request->has('end_date')) {
				$endDate = $request->input('end_date');
				$to = \Carbon\Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d');
			}

			// Validate that we're not requesting future dates with no data
			$today = date('Y-m-d');
			$requestedTo = $to ?: $today;

			// If requesting future dates, adjust to show available data
			if ($requestedTo > $today) {
				$to = $today;
				\Log::warning('Adjusted end date from future to today', [
					'original_to' => $requestedTo,
					'adjusted_to' => $to
				]);
			}

			// Validate partyguid if provided
			$partyGuid = $request->input('partyguid');
			if ($partyGuid) {
				$userByGuid = User::where('guid', $partyGuid)->first();
				if (!$userByGuid || $userByGuid->id != $userId) {
					return $this->error(__("response_message.dashboard.unauthorized_party"), 403);
				}
			}

			// Use ReportsService for graph data with consistent options
			$graphOptions = [
				'outflow_negative' => false, // Match desktop default
				'groups' => null,
				'exclude_types' => null,
				'date_style' => null,
				'fySel' => $fySel,
			];

			$graphData = $this->reportsService->monthlyGraph($userId, $from, $to, $type, $graphOptions);

			// TRANSFORM API RESPONSE TO MATCH DESKTOP STRUCTURE
			$transformedData = [
				'type' => $type,
				'range' => [
					'from' => $from,
					'to' => $to
				],
				'fy_label' => $graphData['fy_label'] ?? '',
				'months' => $graphData['months'] ?? [],
				// Map cashIn/cashOut to in/out for consistency with desktop
				'cashIn' => $graphData['cashIn'] ?? [],
				'cashOut' => $graphData['cashOut'] ?? [],
				'in' => $graphData['cashIn'] ?? [], // Add alias for desktop compatibility
				'out' => $graphData['cashOut'] ?? [], // Add alias for desktop compatibility
				'prevMonthIn' => $graphData['prevMonthIn'] ?? [],
				'prevMonthOut' => $graphData['prevMonthOut'] ?? [],
				'prevQuarterIn' => $graphData['prevQuarterIn'] ?? [],
				'prevQuarterOut' => $graphData['prevQuarterOut'] ?? [],
				'prevYearIn' => $graphData['prevYearIn'] ?? [],
				'prevYearOut' => $graphData['prevYearOut'] ?? [],
				'budgetIn' => $graphData['budgetIn'] ?? [],
				'budgetOut' => $graphData['budgetOut'] ?? [],
				'forecastIn' => $graphData['forecastIn'] ?? [],
				'forecastOut' => $graphData['forecastOut'] ?? [],
				'cashflowIn' => $graphData['cashflowIn'] ?? [],
				'cashflowOut' => $graphData['cashflowOut'] ?? [],
				'plIn' => $graphData['plIn'] ?? [],
				'plOut' => $graphData['plOut'] ?? [],
				'totals' => $graphData['totals'] ?? [],
				'totalsPrev' => $graphData['totalsPrev'] ?? [],
				'allTotals' => $graphData['allTotals'] ?? [],
				'availableGroups' => $graphData['availableGroups'] ?? [],
				'selectedGroups' => $graphData['selectedGroups'] ?? []
			];

			// Add sum calculations to match desktop
			$sum = fn($arr) => array_sum(array_map('floatval', $arr ?? []));
			$transformedData['sumIn'] = $sum($transformedData['cashIn']);
			$transformedData['sumOut'] = $sum($transformedData['cashOut']);

			// Add debug info
			$transformedData['debug'] = [
				'request_dates' => [
					'start_date' => $request->input('start_date'),
					'end_date' => $request->input('end_date'),
					'from' => $from,
					'to' => $to
				],
				'data_available' => $transformedData['sumIn'] > 0 || $transformedData['sumOut'] > 0
			];

			return $this->success(__("response_message.dashboard.graph_data"), $transformedData);
		} catch (\Exception $e) {
			\Log::error('Financial Graphs API Error: ' . $e->getMessage());
			\Log::error('Financial Graphs Request: ', $request->all());
			return $this->error(__("response_message.dashboard.graph_error"), 500, $e->getMessage());
		}
	}

    public function getGroupBalances(Request $request)
    {
        try {
            $user = auth()->user();

            if ($user->role != User::ROLES['client']) {
                return $this->error(__("response_message.dashboard.unauthorized_role"), 403);
            }

            $validator = Validator::make($request->all(), [
                'from' => 'nullable|date_format:Y-m-d',
                'to' => 'nullable|date_format:Y-m-d|after_or_equal:from'
            ]);

            if ($validator->fails()) {
                return $this->error(__("response_message.validation_failed"), 422, $validator->errors());
            }

            $userId = (int) $user->id;
            $from = $request->input('from');
            $to = $request->input('to');

            $groupsWithBalances = $this->reportsService->getAllGroupsWithBalances($userId, $from, $to);

            $groups = collect($groupsWithBalances)->map(function ($group) {
                return [
                    'iGroupId' => (int)$group->iGroupId,
                    'strGroupName' => $group->strGroupName,
                    'Closing' => (float)($group->Closing ?? 0),
                    'Opening' => (float)($group->Opening ?? 0),
                    'accent' => $this->getAccentColor($group->strGroupName),
                    'icon' => $this->getGroupIcon($group->strGroupName)
                ];
            })->values()->toArray();

            return $this->success(__("response_message.dashboard.groups_loaded"), $groups);
        } catch (\Exception $e) {
            return $this->error(__("response_message.dashboard.groups_error"), 500, $e->getMessage());
        }
    }

    // Helper methods from previous HomeController
    // private function getAccentColor($groupName)
    // {
    //     $colors = [
    //         'Sales Accounts' => 'blue',
    //         'Purchase Accounts' => 'amber',
    //         'Sundry Creditors' => 'violet',
    //         'Sundry Debtors' => 'fuchsia',
    //         'Cash-in-Hand' => 'teal',
    //         'Bank Accounts' => 'indigo',
    //         'Direct Incomes' => 'emerald',
    //         'Direct Expenses' => 'rose',
    //         'Duties & Taxes' => 'orange',
    //         'Loans & Advances (Assets)' => 'purple',
    //         'Current Assets' => 'cyan',
    //         'Fixed Assets' => 'lime',
    //         'Stock-in-Hand' => 'yellow',
    //         'Investments' => 'pink',
    //         'Mutual Fund' => 'sky',
    //         'Indirect Incomes' => 'green',
    //         'Indirect Expenses' => 'red',
    //         'Loans & Advances' => 'purple',
    //         'Capital Account' => 'gray',
    //     ];

    //     return $colors[$groupName] ?? 'gray';
    // }

    protected function getAccentColor($groupName)
    {
        $colorMap = [
            'bank' => 'blue',
            'cash' => 'emerald',
            'sales' => 'green',
            'purchase' => 'orange',
            'debtors' => 'violet',
            'creditors' => 'rose',
            'assets' => 'indigo',
            'liabilities' => 'amber',
            'capital' => 'teal',
            'income' => 'lime',
            'expenses' => 'red',
            'tax' => 'purple',
            'stock' => 'cyan',
            'loan' => 'fuchsia',
            'investment' => 'sky',
        ];

        $groupName = strtolower(trim($groupName));
        
        foreach ($colorMap as $key => $color) {
            if (str_contains($groupName, $key)) {
                return $color;
            }
        }
        
        // Default colors rotation
        $defaultColors = ['blue', 'amber', 'violet', 'fuchsia', 'teal', 'indigo', 'emerald', 'rose'];
        $hash = crc32($groupName);
        return $defaultColors[$hash % count($defaultColors)];
    }

    // private function getGroupIcon($groupName)
    // {
    //     $icons = [
    //         'Sales Accounts' => 'fa-solid fa-chart-line',
    //         'Purchase Accounts' => 'fa-solid fa-cart-shopping',
    //         'Sundry Creditors' => 'fa-solid fa-people-group',
    //         'Sundry Debtors' => 'fa-solid fa-user-group',
    //         'Cash-in-Hand' => 'fa-solid fa-wallet',
    //         'Bank Accounts' => 'fa-solid fa-building-columns',
    //         'Direct Incomes' => 'fa-solid fa-money-bill-trend-up',
    //         'Direct Expenses' => 'fa-solid fa-money-bill-transfer',
    //         'Indirect Incomes' => 'fa-solid fa-money-bill-wave',
    //         'Indirect Expenses' => 'fa-solid fa-receipt',
    //         'Duties & Taxes' => 'fa-solid fa-scale-balanced',
    //         'Loans & Advances' => 'fa-solid fa-hand-holding-dollar',
    //         'Loans & Advances (Assets)' => 'fa-solid fa-hand-holding-dollar',
    //         'Current Assets' => 'fa-solid fa-boxes-stacked',
    //         'Fixed Assets' => 'fa-solid fa-building',
    //         'Stock-in-Hand' => 'fa-solid fa-warehouse',
    //         'Investments' => 'fa-solid fa-chart-pie',
    //         'Mutual Fund' => 'fa-solid fa-coins',
    //         'Capital Account' => 'fa-solid fa-landmark',
    //     ];

    //     return $icons[$groupName] ?? 'fa-solid fa-circle-dollar';
    // }

    protected function getGroupIcon($groupName)
    {
        $iconMap = [
            // Financial & Banking
            'bank' => 'fa-solid fa-building-columns',
            'cash' => 'fa-solid fa-money-bill-wave',
            'bank accounts' => 'fa-solid fa-building-columns',
            'cash-in-hand' => 'fa-solid fa-money-bill-wave',
            'current assets' => 'fa-solid fa-chart-line',
            'fixed assets' => 'fa-solid fa-industry',
            'investments' => 'fa-solid fa-chart-pie',
            
            // Sales & Revenue
            'sales' => 'fa-solid fa-tags',
            'sales accounts' => 'fa-solid fa-tags',
            'income' => 'fa-solid fa-money-bill-trend-up',
            'revenue' => 'fa-solid fa-money-bill-wave',
            
            // Purchases & Expenses
            'purchase' => 'fa-solid fa-cart-shopping',
            'purchase accounts' => 'fa-solid fa-cart-shopping',
            'expenses' => 'fa-solid fa-receipt',
            'direct expenses' => 'fa-solid fa-truck',
            'indirect expenses' => 'fa-solid fa-file-invoice-dollar',
            
            // Debtors & Creditors
            'debtors' => 'fa-solid fa-hand-holding-dollar',
            'creditors' => 'fa-solid fa-hand-holding-hand',
            'sundry debtors' => 'fa-solid fa-hand-holding-dollar',
            'sundry creditors' => 'fa-solid fa-hand-holding-hand',
            'receivables' => 'fa-solid fa-arrow-down-to-line',
            'payables' => 'fa-solid fa-arrow-up-from-line',
            
            // Capital & Liabilities
            'capital' => 'fa-solid fa-landmark',
            'liabilities' => 'fa-solid fa-scale-balanced',
            'current liabilities' => 'fa-solid fa-clock-rotate-left',
            'long term liabilities' => 'fa-solid fa-calendar-day',
            
            // Stock & Inventory
            'stock' => 'fa-solid fa-boxes-stacked',
            'inventory' => 'fa-solid fa-warehouse',
            'stock-in-hand' => 'fa-solid fa-boxes-stacked',
            
            // Loans & Advances
            'loans' => 'fa-solid fa-hand-holding-dollar',
            'advances' => 'fa-solid fa-forward',
            'loan' => 'fa-solid fa-hand-holding-dollar',
            'advance' => 'fa-solid fa-forward',
            
            // Tax
            'tax' => 'fa-solid fa-percent',
            'duties' => 'fa-solid fa-scale-balanced',
            'taxes' => 'fa-solid fa-percent',
            
            // General
            'accounts' => 'fa-solid fa-book',
            'ledger' => 'fa-solid fa-book-open',
            'general' => 'fa-solid fa-gear',
            'miscellaneous' => 'fa-solid fa-cube',
            'profit' => 'fa-solid fa-chart-line',
            'loss' => 'fa-solid fa-chart-line-down',
            
            // Default fallbacks
            'assets' => 'fa-solid fa-chart-line',
            'equity' => 'fa-solid fa-scale-balanced',
            'revenue' => 'fa-solid fa-money-bill-wave',
        ];

        $groupName = strtolower(trim($groupName));
        
        // Exact match
        if (isset($iconMap[$groupName])) {
            return $iconMap[$groupName];
        }
        
        // Partial match
        foreach ($iconMap as $key => $icon) {
            if (str_contains($groupName, $key)) {
                return $icon;
            }
        }
        
        // Default icon based on group type
        if (str_contains($groupName, 'asset')) {
            return 'fa-solid fa-chart-line';
        } elseif (str_contains($groupName, 'liabilit')) {
            return 'fa-solid fa-scale-balanced';
        } elseif (str_contains($groupName, 'income') || str_contains($groupName, 'revenue')) {
            return 'fa-solid fa-money-bill-wave';
        } elseif (str_contains($groupName, 'expense') || str_contains($groupName, 'cost')) {
            return 'fa-solid fa-receipt';
        } elseif (str_contains($groupName, 'capital')) {
            return 'fa-solid fa-landmark';
        } elseif (str_contains($groupName, 'bank') || str_contains($groupName, 'cash')) {
            return 'fa-solid fa-building-columns';
        }
        
        // Ultimate fallback
        return 'fa-solid fa-cube';
    }

    // Keep your existing fmt method for number formatting
    private function fmt($v): string
    {
        // ... your existing fmt method implementation ...
        if ($v === null || $v === '') {
            $v = 0;
        }
        $num = is_numeric(trim((string)$v)) ? (float)trim((string)$v) : 0.0;

        if (class_exists(\NumberFormatter::class)) {
            $nf = new \NumberFormatter('en_IN', \NumberFormatter::DECIMAL);
            $nf->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);
            $out = $nf->format($num);
            if ($out !== false) {
                return $out;
            }
        }

        $sign = $num < 0 ? '-' : '';
        $abs  = abs($num);
        $fixed = sprintf('%.2f', $abs);
        [$int, $dec] = explode('.', $fixed);

        if (strlen($int) > 3) {
            $last3 = substr($int, -3);
            $rest  = substr($int, 0, -3);
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
            $int  = $rest . ',' . $last3;
        }
        return $sign . $int . '.' . $dec;
    }
}
