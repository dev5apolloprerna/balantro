<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
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
            // $rows = DB::select('EXEC dbo.usp_GetClientDocumentSummary ?', [$userId]);
            $rows = Cache::remember("api_dashboard:{$userId}:document_summary", now()->addMinutes(5), function () use ($userId) {
                return DB::select('EXEC dbo.usp_GetClientDocumentSummary ?', [$userId]);
            });
            $row = $rows[0] ?? (object) [];

            // Get groups with balances
            // $allGroupsWithBalances = $this->reportsService->getAllGroupsWithBalances($userId, $from, $to);
            $allGroupsWithBalances = Cache::remember("api_dashboard:{$userId}:groups:" . md5(($from ?? '') . '|' . ($to ?? '')), now()->addMinutes(10), function () use ($userId, $from, $to) {
                return $this->reportsService->getAllGroupsWithBalances($userId, $from, $to);
            });
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

    public function yearListing(Request $request)
    {
        try {
            $user = auth()->user();

            if ($user->role != User::ROLES['client']) {
                return $this->error(__("response_message.dashboard.unauthorized_role"), 403);
            }

            $currentFinancialYearStart = Carbon::now()->month >= 4
                ? Carbon::now()->year
                : Carbon::now()->year - 1;
            $currentFinancialYear = sprintf('%d-%04d', $currentFinancialYearStart, $currentFinancialYearStart + 1);

            $years = DB::table('YearMaster')
                ->where('iPartyId', (int) $user->id)
                ->orderBy('iYearId', 'desc')
                ->get()
                ->map(function ($year) use ($currentFinancialYear) {
                    $yearLabel = trim((string) $year->strYear);
                    $from = null;
                    $to = null;

                    if (preg_match('/^(\d{4})-(\d{4})$/', $yearLabel, $matches)) {
                        $from = '01-04-' . $matches[1];
                        $to =  '31-03-' . $matches[2];
                    }

                    return [
                        'iYearId' => (int) $year->iYearId,
                        'key' => $yearLabel,
                        'value' => $yearLabel,
                        'label' => $yearLabel,
                        'from' => $from,
                        'to' => $to,
                        'is_current' => $yearLabel === $currentFinancialYear,
                    ];
                })
                ->values()
                ->toArray();

            return $this->success(__("response_message.dashboard.year_listing"), [
                'years' => $years,
                'current_year' => $currentFinancialYear,
            ]);
        } catch (\Exception $e) {
            return $this->error(__("response_message.dashboard.year_listing_error"), 500, $e->getMessage());
        }
    }

    public function profitLossBalanceSheet(Request $request)
    {
        try {
            $user = auth()->user();

            if ($user->role != User::ROLES['client']) {
                return $this->error(__("response_message.dashboard.unauthorized_role"), 403);
            }

            $validator = Validator::make($request->all(), [
                'from' => 'nullable|date_format:d-m-Y',
                'to' => 'nullable|date_format:d-m-Y|after_or_equal:from',
                'fySel' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->error(__("response_message.validation_failed"), 422, $validator->errors());
            }

            [$from, $to] = $this->resolveDashboardDateRange($request);
            $partyId = (int) $user->id;
            $bankSuspenseCount = DB::table('bank_transactions')
                ->where('is_suspense', 1)
                ->where('iPartyId', $partyId)
                ->count();

            $plResponse = $this->reportsService->pandl($partyId, $from, $to);
            $plData = $plResponse['data'] ?? [];

            $sales = $this->amountFromRows($plData['cr'] ?? [], 'Sales Accounts');
            $directIncome = $this->amountFromRows($plData['cr'] ?? [], 'Direct Incomes');
            $purchase = $this->amountFromRows($plData['dr'] ?? [], 'Purchase Accounts');
            $directExpense = $this->amountFromRows($plData['dr'] ?? [], 'Direct Expenses');
            $indirectIncome = $this->amountFromRows($plData['IndirectIncomes'] ?? [], 'Indirect Incomes');
            $indirectExpense = $this->amountFromRows($plData['IndirectExpenses'] ?? [], 'Indirect Expenses');
            $openingStock = $this->toFloat($plData['OpeningStock'] ?? 0);
            $closingStock = $this->toFloat($plData['ClosingStock'] ?? 0);
            $cogs = $openingStock + $purchase - $closingStock;
            $totalIncome = $sales + $directIncome + $closingStock;
            $totalExpense = $openingStock + $purchase + $directExpense;
            $gross = $totalIncome - $totalExpense;
            $net = $gross + $indirectIncome - $indirectExpense;

            $profitLossItems = [
                ['key' => 'revenue', 'label' => 'Revenue', 'amount' => $sales],
                ['key' => 'cost_of_revenue', 'label' => 'Cost of Revenue', 'amount' => $cogs],
                ['key' => 'direct_income', 'label' => 'Direct Income', 'amount' => $directIncome],
                ['key' => 'indirect_income', 'label' => 'Indirect Income', 'amount' => $indirectIncome],
                ['key' => 'direct_expense', 'label' => 'Direct Expense', 'amount' => $directExpense],
                ['key' => 'indirect_expense', 'label' => 'Indirect Expense', 'amount' => $indirectExpense],
                ['key' => $net >= 0 ? 'profit' : 'loss', 'label' => $net >= 0 ? 'Profit' : 'Loss', 'amount' => abs($net)],
            ];
            $profitLossItems = $this->withPercentages($profitLossItems);

            $bsResponse = $this->reportsService->balanceSheet($user->guid ?? null, $partyId, $from, $to);
            $bsRows = collect($bsResponse['data']['rows'] ?? []);
            $drRows = $bsRows->where('Side', 'DR');
            $crRows = $bsRows->where('Side', 'CR');

            $assets = 0.0;
            foreach ($drRows as $row) {
                $amount = (float) ($row->decMainAmount ?? 0);
                $assets += $amount > 0 ? -1 * $amount : abs($amount);
            }

            $liabilities = 0.0;
            $equity = 0.0;
            foreach ($crRows as $row) {
                $amount = (float) ($row->decMainAmount ?? 0);
                if (in_array($row->strGroupName, ['Capital Account', 'Profit & Loss A/c'], true)) {
                    $equity += $amount;
                } else {
                    $liabilities += $amount;
                }
            }

            $balanceSheetItems = $this->withPercentages([
                ['key' => 'assets', 'label' => 'Assets', 'amount' => abs($assets)],
                ['key' => 'liabilities', 'label' => 'Liabilities', 'amount' => $liabilities],
                ['key' => 'equity', 'label' => 'Equity', 'amount' => $equity],
            ]);

            $rows = Cache::remember("api_dashboard:{$user->id}:document_summary", now()->addMinutes(5), function () use ($user) {
                return DB::select('EXEC dbo.usp_GetClientDocumentSummary ?', [$user->id]);
            });
            $row = $rows[0] ?? (object) [];

            return $this->success(__("response_message.dashboard.financial_summary"), [
                'range' => ['from' => $from, 'to' => $to],
                'bank_suspense_count' => (int) $bankSuspenseCount,
                'document_summary' => [
                    'uploaded_count'    => (int) ($row->uploaded_count    ?? 0),
                    'in_progress_count' => (int) ($row->in_progress_count ?? 0),
                    'completed_count'   => (int) ($row->completed_count   ?? 0),
                    'rejected_count'    => (int) ($row->rejected_count    ?? 0),
                    'accepted_count'    => (int) ($row->accepted_count    ?? 0),
                ],
                'profit_loss' => [
                    'items' => $profitLossItems,
                    'gross_amount' => round($gross, 2),
                    'net_amount' => round($net, 2),
                    'is_profit' => $net >= 0,
                    'raw' => $plData,
                ],
                'balance_sheet' => [
                    'items' => $balanceSheetItems,
                    'total_assets' => round(abs($assets), 2),
                    'total_liabilities' => round($liabilities, 2),
                    'total_equity' => round($equity, 2),
                    'raw' => $bsResponse['data'] ?? [],
                ],
            ]);
        } catch (\Exception $e) {
            return $this->error(__("response_message.dashboard.financial_summary_error"), 500, $e->getMessage());
        }
    }

    public function monthlyFinancialColumns(Request $request)
    {
        try {
            $user = auth()->user();

            if ($user->role != User::ROLES['client']) {
                return $this->error(__("response_message.dashboard.unauthorized_role"), 403);
            }

            $validator = Validator::make($request->all(), [
                'from' => 'nullable|date_format:d-m-Y',
                'to' => 'nullable|date_format:d-m-Y|after_or_equal:from',
                'fySel' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->error(__("response_message.validation_failed"), 422, $validator->errors());
            }

            [$from, $to] = $this->resolveDashboardDateRange($request);
            $partyId = (int) $user->id;

            $salesPurchase = $this->reportsService->monthlyGraph($partyId, $from, $to, 1, [
                'outflow_negative' => false,
                'groups' => null,
                'exclude_types' => null,
                'date_style' => null,
            ]);

            $columns = [
                'sales' => $salesPurchase['cashIn'] ?? [],
                'purchase' => $salesPurchase['cashOut'] ?? [],
            ];

            $metricMap = [
                'direct_income' => 'Direct Incomes',
                'direct_expense' => 'Direct Expenses',
                'indirect_income' => 'Indirect Incomes',
                'indirect_expense' => 'Indirect Expenses',
            ];

            foreach ($metricMap as $key => $metric) {
                $metricData = $this->reportsService->monthlyGraph($partyId, $from, $to, 5, [
                    'metricType' => $metric,
                ]);
                $columns[$key] = $metricData['cashIn'] ?? [];
            }

            $months = $salesPurchase['months'] ?? [];
            $rows = [];
            foreach ($months as $index => $month) {
                $rows[] = [
                    'month' => $month,
                    'sales' => (float) ($columns['sales'][$index] ?? 0),
                    'purchase' => (float) ($columns['purchase'][$index] ?? 0),
                    'direct_income' => (float) ($columns['direct_income'][$index] ?? 0),
                    'direct_expense' => (float) ($columns['direct_expense'][$index] ?? 0),
                    'indirect_income' => (float) ($columns['indirect_income'][$index] ?? 0),
                    'indirect_expense' => (float) ($columns['indirect_expense'][$index] ?? 0),
                ];
            }

            return $this->success(__("response_message.dashboard.monthly_financial_columns"), [
                'range' => ['from' => $from, 'to' => $to],
                'months' => $months,
                'columns' => $columns,
                'rows' => $rows,
                'totals' => array_map(fn($values) => round(array_sum(array_map('floatval', $values)), 2), $columns),
            ]);
        } catch (\Exception $e) {
            return $this->error(__("response_message.dashboard.monthly_financial_columns_error"), 500, $e->getMessage());
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
                ],
                'Bargraph' => [
                    ['key' => 'sales', 'value' => "Sales"],
                    ['key' => 'purchase', 'value' => "Purchase"],
                    ['key' => 'direct_income', 'value' => "Direct Income"],
                    ['key' => 'direct_expense', 'value' => "Direct Expense"],
                    ['key' => 'indirect_income', 'value' => "Indirect Income"],
                    ['key' => 'indirect_expense', 'value' => "Indirect Expense"],
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

			// $graphData = $this->reportsService->monthlyGraph($userId, $from, $to, $type, $graphOptions);
            $graphData = Cache::remember("api_dashboard:{$userId}:graph:" . md5($type . '|' . ($from ?? '') . '|' . ($to ?? '') . '|' . json_encode($graphOptions)), now()->addMinutes(10), function () use ($userId, $from, $to, $type, $graphOptions) {
				return $this->reportsService->monthlyGraph($userId, $from, $to, $type, $graphOptions);
			});

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

    // public function getGroupBalances(Request $request)
    // {
    //     try {
    //         $user = auth()->user();

    //         if ($user->role != User::ROLES['client']) {
    //             return $this->error(__("response_message.dashboard.unauthorized_role"), 403);
    //         }

    //         $validator = Validator::make($request->all(), [
    //             'from' => 'nullable|date_format:Y-m-d',
    //             'to' => 'nullable|date_format:Y-m-d|after_or_equal:from'
    //         ]);

    //         if ($validator->fails()) {
    //             return $this->error(__("response_message.validation_failed"), 422, $validator->errors());
    //         }

    //         $userId = (int) $user->id;
    //         $from = $request->input('from');
    //         $to = $request->input('to');

    //         // $groupsWithBalances = $this->reportsService->getAllGroupsWithBalances($userId, $from, $to);
    //         $groupsWithBalances = Cache::remember("api_dashboard:{$userId}:group_balances:" . md5(($from ?? '') . '|' . ($to ?? '')), now()->addMinutes(10), function () use ($userId, $from, $to) {
    //             return $this->reportsService->getAllGroupsWithBalances($userId, $from, $to);
    //         });

    //         $groups = collect($groupsWithBalances)->map(function ($group) {
    //             return [
    //                 'iGroupId' => (int)$group->iGroupId,
    //                 'strGroupName' => $group->strGroupName,
    //                 'Closing' => (float)($group->Closing ?? 0),
    //                 'Opening' => (float)($group->Opening ?? 0),
    //                 'accent' => $this->getAccentColor($group->strGroupName),
    //                 'icon' => $this->getGroupIcon($group->strGroupName)
    //             ];
    //         })->values()->toArray();

    //         return $this->success(__("response_message.dashboard.groups_loaded"), $groups);
    //     } catch (\Exception $e) {
    //         return $this->error(__("response_message.dashboard.groups_error"), 500, $e->getMessage());
    //     }
    // }

    public function getGroupBalances(Request $request)
    {
        try {
            $user = auth()->user();

            if ($user->role != User::ROLES['client']) {
                return $this->error(__("response_message.dashboard.unauthorized_role"), 403);
            }

            $validator = Validator::make($request->all(), [
                'from' => 'nullable|date_format:d-m-Y',
                'to' => 'nullable|date_format:d-m-Y|after_or_equal:from'
            ]);

            if ($validator->fails()) {
                return $this->error(__("response_message.validation_failed"), 422, $validator->errors());
            }

            $userId = (int) $user->id;
            $from = $request->input('from');
            $to = $request->input('to');
            [$from, $to] = $this->resolveDashboardDateRange($request);

            $defaultGroupNames = [
                'Sales Accounts',
                'Purchase Accounts',
                'Sundry Creditors',
                'Sundry Debtors',
                'Cash-in-Hand',
                'Bank Accounts',
                'Direct Incomes',
                'Direct Expenses',
            ];
            $allGroups = collect($this->reportsService->getAllGroupsWithBalances($userId, $from, $to));
           
            $defaultGroupIds = $allGroups
                ->whereIn('strGroupName', $defaultGroupNames)
                ->pluck('iGroupId')
                ->map(fn($groupId) => (int) $groupId)
                ->values()
                ->toArray();

            if (empty($defaultGroupIds)) {
                $defaultGroupIds = $allGroups
                    ->take(8)
                    ->pluck('iGroupId')
                    ->map(fn($groupId) => (int) $groupId)
                    ->values()
                    ->toArray();
            }

            $preferences = UserCardPreference::where('user_id', $userId)
                ->where('party_id', $userId)
                ->first();

            if ($preferences && $preferences->selected_groups) {
                $selectedGroups = is_array($preferences->selected_groups)
                    ? $preferences->selected_groups
                    : json_decode($preferences->selected_groups, true);
                $selectedGroups = array_map('intval', $selectedGroups ?? []);
            } else {
                $selectedGroups = $defaultGroupIds;
            }

            $validSelectedGroups = [];
            foreach ($selectedGroups as $groupId) {
                if ($allGroups->contains('iGroupId', $groupId)) {
                    $validSelectedGroups[] = (int) $groupId;
                }
            }

            if (empty($validSelectedGroups)) {
                $validSelectedGroups = $defaultGroupIds;
            }

            $groups = $allGroups
                ->whereIn('iGroupId', $validSelectedGroups)
                ->map(function ($group) {
                    return [
                        'iGroupId' => (int) $group->iGroupId,
                        'strGroupName' => $group->strGroupName,
                        'Closing' => (float) ($group->Closing ?? 0),
                        'Opening' => (float) ($group->Opening ?? 0),
                        'accent' => $this->getAccentColor($group->strGroupName),
                        'icon' => $this->getGroupIcon($group->strGroupName),
                    ];
                })
                ->values()
                ->toArray();

            $selectedGroupsWithBalances = collect($groups)->map(function ($group) {
                return [
                    'iGroupId' => $group['iGroupId'],
                    'strGroupName' => $group['strGroupName'],
                    'Closing' => $group['Closing'],
                    'Opening' => $group['Opening'],
                ];
            })->values()->toArray();

            $groupCards = collect($groups)->map(function ($group) {
                return [
                    'key' => 'group_' . $group['iGroupId'],
                    'iGroupId' => $group['iGroupId'],
                    'value' => $group['Closing'],
                    'name' => $group['strGroupName'],
                    'label' => $group['strGroupName'],
                    'accent' => $group['accent'],
                    'icon' => $group['icon'],
                    'opening_balance' => $group['Opening'],
                    'closing_balance' => $group['Closing'],
                ];
            })->values()->toArray();

            return $this->success(__("response_message.dashboard.groups_loaded"), [
                'range' => ['from' => $from, 'to' => $to],
                'groups' => $groups,
                'selected_group_ids' => $validSelectedGroups,
                'selected_groups_with_balances' => $selectedGroupsWithBalances,
                'default_group_ids' => $defaultGroupIds,
                'group_cards' => $groupCards,
            ]);
        } catch (\Exception $e) {
            return $this->error(__("response_message.dashboard.groups_error"), 500, $e->getMessage());
        }
    }

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

    protected function getGroupIcon($groupName)
    {
        $iconMap = [
            // Financial & Banking
            // 'bank' => 'fa-solid fa-building-columns',
            // 'cash' => 'fa-solid fa-money-bill-wave',
            // 'bank accounts' => 'fa-solid fa-building-columns',
            // 'cash-in-hand' => 'fa-solid fa-money-bill-wave',
            // 'current assets' => 'fa-solid fa-chart-line',
            // 'fixed assets' => 'fa-solid fa-industry',
            // 'investments' => 'fa-solid fa-chart-pie',
            
            // // Sales & Revenue
            // 'sales' => 'fa-solid fa-tags',
            // 'sales accounts' => 'fa-solid fa-tags',
            // 'income' => 'fa-solid fa-money-bill-trend-up',
            // 'revenue' => 'fa-solid fa-money-bill-wave',
            
            // // Purchases & Expenses
            // 'purchase' => 'fa-solid fa-cart-shopping',
            // 'purchase accounts' => 'fa-solid fa-cart-shopping',
            // 'expenses' => 'fa-solid fa-receipt',
            // 'direct expenses' => 'fa-solid fa-truck',
            // 'indirect expenses' => 'fa-solid fa-file-invoice-dollar',
            
            // // Debtors & Creditors
            // 'debtors' => 'fa-solid fa-hand-holding-dollar',
            // 'creditors' => 'fa-solid fa-hand-holding-hand',
            // 'sundry debtors' => 'fa-solid fa-hand-holding-dollar',
            // 'sundry creditors' => 'fa-solid fa-hand-holding-hand',
            // 'receivables' => 'fa-solid fa-arrow-down-to-line',
            // 'payables' => 'fa-solid fa-arrow-up-from-line',
            
            // // Capital & Liabilities
            // 'capital' => 'fa-solid fa-landmark',
            // 'liabilities' => 'fa-solid fa-scale-balanced',
            // 'current liabilities' => 'fa-solid fa-clock-rotate-left',
            // 'long term liabilities' => 'fa-solid fa-calendar-day',
            
            // // Stock & Inventory
            // 'stock' => 'fa-solid fa-boxes-stacked',
            // 'inventory' => 'fa-solid fa-warehouse',
            // 'stock-in-hand' => 'fa-solid fa-boxes-stacked',
            
            // // Loans & Advances
            // 'loans' => 'fa-solid fa-hand-holding-dollar',
            // 'advances' => 'fa-solid fa-forward',
            // 'loan' => 'fa-solid fa-hand-holding-dollar',
            // 'advance' => 'fa-solid fa-forward',
            
            // // Tax
            // 'tax' => 'fa-solid fa-percent',
            // 'duties' => 'fa-solid fa-scale-balanced',
            // 'taxes' => 'fa-solid fa-percent',
            
            // // General
            // 'accounts' => 'fa-solid fa-book',
            // 'ledger' => 'fa-solid fa-book-open',
            // 'general' => 'fa-solid fa-gear',
            // 'miscellaneous' => 'fa-solid fa-cube',
            // 'profit' => 'fa-solid fa-chart-line',
            // 'loss' => 'fa-solid fa-chart-line-down',
            
            // // Default fallbacks
            // 'assets' => 'fa-solid fa-chart-line',
            // 'equity' => 'fa-solid fa-scale-balanced',
            // 'revenue' => 'fa-solid fa-money-bill-wave',

            'bank' => 'bank-od.png',
            'cash' => 'cash.png',
            'bank accounts' => 'bank-account.png',
            'cash-in-hand' => 'cash-in-hand.png',
            'current assets' => 'current-assets.png',
            'fixed assets' => 'fixed-assets.png',
            'investments' => 'investment.png',
            'current liabilities' => 'current-laibities.png',
            'deposits (asset)' => 'deposit.png',
            'provisions' => 'provision.png',
            'reserves & surplus' => 'reserve.png',
            'stock-in-hand' => 'stock-in-hand.png',
            'sundry creditors' => 'sundry-creditors.png',
            'sundry debtors' => 'sundry-debitors.png',
            'sales' => 'sales.png',
            'income' => 'income.png',
            'suspense a/c' => 'suspense-acc.png',
            'purchase' => 'purchase.png',
            'expenses' => 'expenses.png',
            'secured loans' => 'secured-loan.png',
            'unsecured loans' => 'unsecured-loan.png',
            'debtors' => 'debtors.png',
            'creditors' => 'creditors.png',
            'capital' => 'capital.png',
            'stock' => 'stock.png',
            'loans' => 'loan(laibility).png',
            'loans & advances (asset)' => 'loans_advance.png',
            'tax' => 'tax.png',
            'duties & taxes' => 'duties&taxes.png',
            'direct expenses' => 'direct_expense.png',
            'direct incomes' => 'direct_income.png',
            'indirect expense' => 'indirect_expense.png',
            'indirect incomes' => 'indirect_income.png',
        ];

        $groupName = strtolower(trim($groupName));
        
        // Exact match
        if (isset($iconMap[$groupName])) {
            //return $iconMap[$groupName];
            return asset('assets/images/' . $iconMap[$groupName]);
        }
        
        // Partial match
        foreach ($iconMap as $key => $icon) {
            if (str_contains($groupName, $key)) {
                //return $icon;
                return asset('assets/images/' . $icon);
            }
        }
        
        // Default icon based on group type
        // if (str_contains($groupName, 'asset')) {
        //     return 'fa-solid fa-chart-line';
        // } elseif (str_contains($groupName, 'liabilit')) {
        //     return 'fa-solid fa-scale-balanced';
        // } elseif (str_contains($groupName, 'income') || str_contains($groupName, 'revenue')) {
        //     return 'fa-solid fa-money-bill-wave';
        // } elseif (str_contains($groupName, 'expense') || str_contains($groupName, 'cost')) {
        //     return 'fa-solid fa-receipt';
        // } elseif (str_contains($groupName, 'capital')) {
        //     return 'fa-solid fa-landmark';
        // } elseif (str_contains($groupName, 'bank') || str_contains($groupName, 'cash')) {
        //     return 'fa-solid fa-building-columns';
        // }
        
        // Ultimate fallback
        // return 'fa-solid fa-cube';
        return asset('assets/images/document.png');
    }

    private function resolveDashboardDateRange(Request $request): array
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $fySel = $request->input('fySel');

        if ((!$from || !$to) && $fySel && preg_match('/^(\d{4})-(\d{4})$/', $fySel, $matches)) {
            $from = $from ?: $matches[1] . '-04-01';
            $to = $to ?: $matches[2] . '-03-31';
        }

        if (!$from || !$to) {
            $today = Carbon::today();
            $startYear = $today->month >= 4 ? $today->year : $today->year - 1;
            $from = $from ?: $startYear . '-04-01';
            $to = $to ?: ($startYear + 1) . '-03-31';
        }

        return [$from, $to];
    }

    private function amountFromRows(array $rows, string $groupName): float
    {
        foreach ($rows as $row) {
            $row = (array) $row;
            if (($row['strGroupName'] ?? null) === $groupName) {
                return $this->toFloat($row['decMainAmount'] ?? 0);
            }
        }

        return 0.0;
    }

    private function toFloat($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        return (float) str_replace(',', '', (string) $value);
    }

    private function withPercentages(array $items): array
    {
        $total = array_sum(array_map(fn($item) => abs((float) ($item['amount'] ?? 0)), $items));

        return array_map(function ($item) use ($total) {
            $amount = (float) ($item['amount'] ?? 0);
            $item['amount'] = round($amount, 2);
            $item['formatted_amount'] = $this->fmt($amount);
            $item['percentage'] = $total > 0 ? round((abs($amount) / $total) * 100, 2) : 0.0;

            return $item;
        }, $items);
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
