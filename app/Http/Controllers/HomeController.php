<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Document;
use App\Models\User;
use App\Services\ManagerDocumentsService;
use App\Models\Client;
use App\Models\DataEntryOperator;
use App\Models\Supervisor;
use App\Models\Manager;
use App\Services\ReportsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCardPreference;
use App\Models\Group;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $r, ReportsService $svc)
    {
        // dd("Hi")
        $user    = auth()->user();
        
        if (auth()->user()->role == User::ROLES['super_admin']) {
            $clientIds = \App\Models\User::where('role', \App\Models\User::ROLES['client'])
                ->pluck('id');

            $counts = \App\Models\Document::whereIn('user_id', $clientIds)
                ->groupBy('status')
                ->selectRaw('status, COUNT(*) AS count')
                ->pluck('count', 'status');
            $summary = [
                ['key' => '1', 'value' => "Sale & Purchase"],
                ['key' => '2', 'value' => "Credit & Debit"],
                ['key' => '3', 'value' => "Recepit & Payment"],
                ['key' => '4', 'value' => "Cash & Bank balance"]
            ];
            // $counts = Document::groupBy('status')->selectRaw('status, count(*) as count')->pluck('count', 'status');
            return view('home', [
                'uploaded_count' => $counts['uploaded'] ?? 0,
                'accepted_count' => $counts['accepted'] ?? 0,
                'rejected_count' => $counts['rejected'] ?? 0,
                'data_entry_in_progress_count' => $counts['data_entry_in_progress'] ?? 0,
                'data_entered_count' => $counts['data_entry_completed'] ?? 0,
                'query_raised_count' => $counts['query_raised'] ?? 0,
                'query_resolved_count' => $counts['query_resolved'] ?? 0,
                'completed_count' => $counts['approved'] ?? 0,
                'clients' => User::where('role', User::ROLES['client'])->get(),
                'managers' => User::where('role', User::ROLES['manager'])->get(),
                'supervisors' => User::where('role', User::ROLES['supervisor'])->get(),
                'data_entry_operators' => User::where('role', User::ROLES['data_entry_operator'])->get(),
                'summary' => $summary
            ]);
        } else if (auth()->user()->role == User::ROLES['supervisor']) {
            $counts = Document::whereIn('user_id', $this->clientIds())
                ->groupBy('status')
                ->selectRaw('status, count(*) as count')
                ->pluck('count', 'status');

            return view('home', [
                'uploaded_count' => $counts['uploaded'] ?? 0,
                'accepted_count' => $counts['accepted'] ?? 0,
                'rejected_count' => $counts['rejected'] ?? 0,
                'data_entry_in_progress_count' => $counts['data_entry_in_progress'] ?? 0,
                'data_entered_count' => $counts['data_entry_completed'] ?? 0,
                'query_raised_count' => $counts['query_raised'] ?? 0,
                'query_resolved_count' => $counts['query_resolved'] ?? 0,
                'completed_count' => $counts['approved'] ?? 0,
                'clients' => $this->supervisorClients(),
                'data_entry_operators' => $user->dataEntryOperators ?? 0
            ]);
        } else if (auth()->user()->role == User::ROLES['manager']) {
            // $documentService = new ManagerDocumentsService;
            // $counts = $documentService->getGroupedByStatus();
            // $user = auth()->user();

            $manager = Manager::where('id', $user->id)->firstOrFail();

            $documentService = new ManagerDocumentsService($manager);  // ✅ pass it
            //$counts = $documentService->getGroupedByStatus();
            $counts = $documentService->groupedByStatus()->all(); // ->all() to get array


            return view('home', [
                'uploaded_count' => $counts['uploaded'] ?? 0,
                'accepted_count' => $counts['accepted'] ?? 0,
                'rejected_count' => $counts['rejected'] ?? 0,
                'data_entry_in_progress_count' => $counts['data_entry_in_progress'] ?? 0,
                'data_entered_count' => $counts['data_entry_completed'] ?? 0,
                'query_raised_count' => $counts['query_raised'] ?? 0,
                'query_resolved_count' => $counts['query_resolved'] ?? 0,
                'completed_count' => $counts['approved'] ?? 0,
                'clients' => Client::whereHas('supervisors', function ($query) use ($user) {
                    $query->whereIn('id', $user->supervisors->pluck('id'));
                })->with(['supervisors', 'dataEntryOperators'])->latest()->get(),
                'supervisors' => $user->supervisors,
                'data_entry_operators' => DataEntryOperator::whereHas('managers', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })->orWhereHas('supervisors', function ($query) use ($user) {
                    $query->whereIn('id', $user->supervisors->pluck('id'));
                })->with(['managers', 'supervisors'])->latest()->get()
            ]);
        } else if (auth()->user()->role == User::ROLES['data_entry_operator']) {

            // 1) Get the DEO’s client USERS
            $clients = $user->clientsAsDataEntryOperator()
                ->with(['managers', 'supervisors'])   // these should be valid relations on User (client)
                ->orderByDesc('created_at')
                ->get();

            // 2) Count documents by client user_id
            $clientUserIds = $user->clientsAsDataEntryOperator()->pluck('id');

            $counts = \App\Models\Document::whereIn('user_id', $clientUserIds)
                ->selectRaw('status, COUNT(*) as c')
                ->groupBy('status')
                ->pluck('c', 'status');

            return view('home', [
                'clients'                        => $clients,
                'uploaded_count'                 => $counts['uploaded'] ?? 0,
                'accepted_count'                 => $counts['accepted'] ?? 0,
                'rejected_count'                 => $counts['rejected'] ?? 0,
                'data_entry_in_progress_count'   => $counts['data_entry_in_progress'] ?? 0,
                'data_entered_count'             => $counts['data_entry_completed'] ?? 0,
                'query_raised_count'             => $counts['query_raised'] ?? 0,
                'query_resolved_count'           => $counts['query_resolved'] ?? 0,
                'completed_count'                => $counts['approved'] ?? 0,
            ]);
        } else if (auth()->user()->role == User::ROLES['client']) {
            $user   = auth()->user();
            $userId = (int) $user->id;
            $partyId = $userId;
            // $from = $r->input('from');
            // $to   = $r->input('to');
            // Financial year selection
            $range = $r->input('range');
            // Store selection in session
            if ($range) {
                session([
                    'selectedRange' => $range,
                    'selectedFrom'  => $r->input('from'),
                    'selectedTo'    => $r->input('to'),
                ]);
            }
            $financialYears = DB::table('YearMaster')
                ->where('iPartyId', $userId)
                ->orderBy('iYearId', 'desc')
                ->get();
            // Restore from session if empty
            $defaultRange = $financialYears->first()->strYear ?? null;
            $selectedRange = $range ?: session('selectedRange', $defaultRange);
            $from = $r->input('from', session('selectedFrom'));
            $to   = $r->input('to', session('selectedTo'));
            if ((! $from || ! $to) && preg_match('/^(\d{4})-(\d{4})$/', (string) $selectedRange, $matches)) {
                $from = $matches[1] . '-04-01';
                $to = $matches[2] . '-03-31';
            }

            if ($selectedRange || $from || $to) {
                session([
                    'selectedRange' => $selectedRange,
                    'selectedFrom'  => $from,
                    'selectedTo'    => $to,
                ]);
            }

            $activeTab = $r->get('tab') === 'documents' ? 'documents' : 'financial';
            $type = (int) $r->input('type', 1);

            $documentCounts = Document::where('user_id', $userId)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            $uploadedCount   = (int) ($documentCounts['uploaded'] ?? 0);
            $inProgressCount = (int) (($documentCounts['accepted'] ?? 0)
                + ($documentCounts['data_entry_in_progress'] ?? 0)
                + ($documentCounts['data_entry_completed'] ?? 0)
                + ($documentCounts['query_raised'] ?? 0)
                + ($documentCounts['query_resolved'] ?? 0));
            $completedCount  = (int) ($documentCounts['approved'] ?? 0);
            $rejectedCount   = (int) ($documentCounts['rejected'] ?? 0);
            $acceptedCount   = (int) ($documentCounts['accepted'] ?? 0);

            if ($activeTab === 'documents') {
                return view('home', [
                    'uploaded_count'    => $uploadedCount,
                    'in_progress_count' => $inProgressCount,
                    'completed_count'   => $completedCount,
                    'rejected_count'    => $rejectedCount,
                    'accepted_count'    => $acceptedCount,
                    'active_tab'        => 'documents',
                    'partyId'           => $partyId,
                    'activeType'        => $type,
                    'fyRangeSel'        => $selectedRange,
                    'financialYears'    => $financialYears,
                ]);
            }

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

            // Fetch ALL groups for this party (not just the 8 financial groups)
            try {
                $allGroupsWithBalances = $svc->getAllGroupsWithBalances($userId, $from, $to);
                $allGroups = collect($allGroupsWithBalances);
                
                // If still no groups, create some default groups for demo
                if ($allGroups->isEmpty()) {
                    \Log::warning('No groups with balances found, creating demo groups');

                    // Create demo groups structure
                    $allGroups = collect([
                        (object)['iGroupId' => 1, 'strGroupName' => 'Sales Accounts', 'Closing' => 100000, 'Opening' => 80000],
                        (object)['iGroupId' => 2, 'strGroupName' => 'Purchase Accounts', 'Closing' => 75000, 'Opening' => 60000],
                        (object)['iGroupId' => 3, 'strGroupName' => 'Sundry Creditors', 'Closing' => 50000, 'Opening' => 45000],
                        (object)['iGroupId' => 4, 'strGroupName' => 'Sundry Debtors', 'Closing' => 60000, 'Opening' => 55000],
                        (object)['iGroupId' => 5, 'strGroupName' => 'Cash-in-Hand', 'Closing' => 25000, 'Opening' => 20000],
                        (object)['iGroupId' => 6, 'strGroupName' => 'Bank Accounts', 'Closing' => 150000, 'Opening' => 120000],
                        (object)['iGroupId' => 7, 'strGroupName' => 'Direct Incomes', 'Closing' => 30000, 'Opening' => 25000],
                        (object)['iGroupId' => 8, 'strGroupName' => 'Direct Expenses', 'Closing' => 45000, 'Opening' => 40000],
                    ]);
                }
            } catch (\Exception $e) {
                
                // Fallback: try to get groups without balances
                $allGroups = DB::table('GroupMaster')
                    ->where('iPartyId', $partyId)
                    ->select('iGroupId', 'strGroupName')
                    ->orderBy('strGroupName')
                    ->get();

                if ($allGroups->isEmpty()) {
                    // Ultimate fallback: create demo groups
                    $allGroups = collect([
                        (object)['iGroupId' => 1, 'strGroupName' => 'Sales Accounts', 'Closing' => 0, 'Opening' => 0],
                        (object)['iGroupId' => 2, 'strGroupName' => 'Purchase Accounts', 'Closing' => 0, 'Opening' => 0],
                        (object)['iGroupId' => 3, 'strGroupName' => 'Sundry Creditors', 'Closing' => 0, 'Opening' => 0],
                        (object)['iGroupId' => 4, 'strGroupName' => 'Sundry Debtors', 'Closing' => 0, 'Opening' => 0],
                        (object)['iGroupId' => 5, 'strGroupName' => 'Cash-in-Hand', 'Closing' => 0, 'Opening' => 0],
                        (object)['iGroupId' => 6, 'strGroupName' => 'Bank Accounts', 'Closing' => 0, 'Opening' => 0],
                        (object)['iGroupId' => 7, 'strGroupName' => 'Direct Incomes', 'Closing' => 0, 'Opening' => 0],
                        (object)['iGroupId' => 8, 'strGroupName' => 'Direct Expenses', 'Closing' => 0, 'Opening' => 0],
                    ]);
                } else {
                    // Add zero balances to groups without balance data
                    $allGroups = $allGroups->map(function ($group) {
                        $group->Closing = 0;
                        $group->Opening = 0;
                        return $group;
                    });
                }
            }

            // Get the IDs of the 8 default financial groups
            $defaultGroupIds = $allGroups
                ->whereIn('strGroupName', $defaultGroupNames)
                ->pluck('iGroupId')
                ->toArray();

            // If no default groups found, use first 8 groups
            if (empty($defaultGroupIds)) {
                $defaultGroupIds = $allGroups->take(8)->pluck('iGroupId')->toArray();
            }

            // Summary labels (unchanged)
            $summary = [
                ['key' => '1', 'value' => "Sale & Purchase"],
                ['key' => '2', 'value' => "Credit & Debit"],
                ['key' => '3', 'value' => "Recepit & Payment"],
                ['key' => '4', 'value' => "Cash & Bank balance"]
            ];

            // $from = $r->input('from');
            // $to   = $r->input('to');

            // Use the resolved range dates so dashboard charts and P&L load on first visit.
            $from = $from ?: session('selectedFrom');
            $to   = $to ?: session('selectedTo');

            $titles = [
                1 => 'Sales vs Purchase',
                2 => 'Creditors vs Debtors',
                3 => 'Receipt vs Payment',
                4 => 'Cash & Bank Flow',
                5 => 'Income & Expense',
            ];

            $charts      = [];
            $selectedRes = null;
            $sum         = fn($arr) => array_sum(array_map('floatval', $arr ?? []));

            for ($t = 1; $t <= 5; $t++) {
                $groups = null;
                $res = [];
                if ($t == 4)
                {
                    $metric = $r->input('metric', 'cash');

                    if ($metric == 'cash') {
                        $groups = 'Cash-in-Hand';
                    }
                    elseif ($metric == 'bank') {
                        $groups = 'Bank Accounts';
                    }
                } elseif ($t == 5) {

                    // $res = [
                    //     'months' => [],
                    //     'directIncome' => [],
                    //     'directExpense' => [],
                    //     'indirectIncome' => [],
                    //     'indirectExpense' => [],
                    // ];

                    $metrics = [
                        'Direct Incomes',
                        'Direct Expenses',
                        'Indirect Incomes',
                        'Indirect Expenses'
                    ];

                    foreach ($metrics as $metric) {

                        $tmp = $svc->monthlyGraph(
                            $userId,
                            $from,
                            $to,
                            5,
                            [
                                'metricType' => $metric
                            ]
                        );
                        
                        $res['months'] = $tmp['months'] ?? [];

                        if ($metric == 'Direct Incomes') {
                            //$res['directIncome'] = $tmp['closingBalance'] ?? [];
                            $res['directIncome'] = $tmp['cashIn'] ?? [];
                        } elseif ($metric == 'Direct Expenses') {
                            //$res['directExpense'] = $tmp['closingBalance'] ?? [];
                            $res['directExpense'] = $tmp['cashIn'] ?? [];
                        } elseif ($metric == 'Indirect Incomes') {
                            //$res['indirectIncome'] = $tmp['closingBalance'] ?? [];
                            $res['indirectIncome'] = $tmp['cashIn'] ?? [];
                        } elseif ($metric == 'Indirect Expenses') {
                            // $res['indirectExpense'] = $tmp['closingBalance'] ?? [];
                            $res['indirectExpense'] = $tmp['cashIn'] ?? [];
                        }
                        
                    }
                }
                
                if ($t != 5) {
                    $res = $svc->monthlyGraph($userId, $from, $to, $t, [
                        'outflow_negative' => false,
                        'groups'           => $groups,
                        'exclude_types'    => null,
                        'date_style'       => null,
                    ]);
                }

                if ($t === $type) {
                    $selectedRes = $res;
                }

                $charts[] = [
                    'key'            => $t,
                    'title'          => $titles[$t],
                    'months'         => $res['months'] ?? [],
                    'in'             => $res['cashIn']  ?? [],
                    'out'            => $res['cashOut'] ?? [],
                    'cash'           => $res['closingBalance'] ?? [],
                    'bank'           => $res['closingBalance'] ?? [],
                    // FIXED: Pass the monthly arrays, not the totals
                    'prevMonthIn'    => $res['prevMonthIn'] ?? [],
                    'prevMonthOut'   => $res['prevMonthOut'] ?? [],
                    'prevQuarterIn'  => $res['prevQuarterIn'] ?? [],
                    'prevQuarterOut' => $res['prevQuarterOut'] ?? [],
                    'prevYearIn'     => $res['prevYearIn'] ?? [],
                    'prevYearOut'    => $res['prevYearOut'] ?? [],
                    'budgetIn'       => $res['budgetIn'] ?? [],
                    'budgetOut'      => $res['budgetOut'] ?? [],
                    'forecastIn'     => $res['forecastIn'] ?? [],
                    'forecastOut'    => $res['forecastOut'] ?? [],
                    'cashflowIn'     => $res['cashflowIn'] ?? [],
                    'cashflowOut'    => $res['cashflowOut'] ?? [],
                    'plIn'           => $res['plIn'] ?? [],
                    'plOut'          => $res['plOut'] ?? [],
                    'sumIn'          => $sum($res['cashIn'] ?? []),
                    'sumOut'         => $sum($res['cashOut'] ?? []),
                    'quarterLabels'  => $res['quarterLabels'] ?? [],
                    'quarterIn'      => $res['quarterIn'] ?? [],
                    'quarterOut'     => $res['quarterOut'] ?? [],
                    'quarterCompare' => $res['quarterCompare'] ?? [],
                    'directIncome'    => $res['directIncome'] ?? [],
                    'directExpense'   => $res['directExpense'] ?? [],
                    'indirectIncome'  => $res['indirectIncome'] ?? [],
                    'indirectExpense' => $res['indirectExpense'] ?? [],
                    // 'prevQuarterIn'  => $res['prevQuarterIn'] ?? [],
                    // 'prevQuarterOut' => $res['prevQuarterOut'] ?? [],
                ];
            }
            
            $basis = $selectedRes ?: ($charts[$type - 1] ?? []);
            $labelFY   = $selectedRes['fy_label']  ?? ($basis['fy_label'] ?? '');
            $range     = $selectedRes['range']     ?? ($basis['range'] ?? ['from' => $from, 'to' => $to]);
            $allTotals = $selectedRes['allTotals'] ?? ($basis['allTotals'] ?? []);
            $fySel     = $r->input('fySel');

            // Get selected groups from database
            $preferences = DB::table('user_card_preferences')
                ->where('user_id', $userId)
                ->where('party_id', $userId)
                ->first();

            if ($preferences && $preferences->selected_groups) {
                $selectedGroups = json_decode($preferences->selected_groups, true);
                $selectedGroups = array_map('intval', $selectedGroups);
            } else {
                $selectedGroups = $defaultGroupIds;
            }

            // Update session for consistency
            $sessionKey = "user_{$userId}_selected_groups";
            session([$sessionKey => $selectedGroups]);

            // Ensure selected groups are valid (exist in user's groups)
            $validSelectedGroups = [];
            foreach ($selectedGroups as $groupId) {
                if ($allGroups->contains('iGroupId', $groupId)) {
                    $validSelectedGroups[] = $groupId;
                }
            }

            // If no valid groups found, use defaults
            if (empty($validSelectedGroups)) {
                $validSelectedGroups = $defaultGroupIds;
            }

            $selectedGroups = $validSelectedGroups;
            $selectedGroupsWithBalances = $allGroups->whereIn('iGroupId', $selectedGroups)
                ->map(function ($group) {
                    return [
                        'iGroupId' => (int)$group->iGroupId,
                        'strGroupName' => $group->strGroupName,
                        'Closing' => (float)($group->Closing ?? 0),
                        'Opening' => (float)($group->Opening ?? 0),
                    ];
                })
                ->values()
                ->toArray();

            // Create group cards data from selected groups with balances
            $allGroupCards = [];
            foreach ($selectedGroupsWithBalances as $group) {
                $allGroupCards[] = [
                    'key' => 'group_' . $group['iGroupId'],
                    'iGroupId' => $group['iGroupId'],
                    'value' => $group['Closing'],
                    'name' => $group['strGroupName'],
                    'label' => $group['strGroupName'],
                    'accent' => $this->getAccentColor($group['strGroupName']),
                    'icon' => $this->getGroupIcon($group['strGroupName'])
                ];
            }

            if ($activeTab === 'financial') {
                $svc = new ReportsService();
                $resp = $svc->pandl($r->user()->id, $from, $to);
                $guid = Auth::user()->guid ?? '';
                $partyguid = $guid;                
                $partyId   = Auth::user()->id;
                
                $respbalance = $svc->balanceSheet($partyguid, $partyId, $from, $to);
                
                return view('home', [
                    'uploaded_count'    => $uploadedCount,
                    'in_progress_count' => $inProgressCount,
                    'completed_count'   => $completedCount,
                    'rejected_count'    => $rejectedCount,
                    'summary'           => $summary,
                    'activeType'        => $type,
                    'charts'            => $charts,
                    'labelFY'           => $labelFY,
                    'range'             => $range,
                    'allTotals'         => $allTotals,
                    'fySel'             => $fySel,
                    'active_tab'        => 'financial',
                    'partyId'           => $partyId,
                    'allGroups'         => $allGroups,
                    'selectedGroups'    => $selectedGroups,
                    'selectedGroupsWithBalances' => $selectedGroupsWithBalances,
                    'defaultGroupIds'   => $defaultGroupIds,
                    'allGroupCards'     => $allGroupCards, // Add this
                    'plData' => $resp['data'] ?? [],
                    'bsData' => $respbalance['data'] ?? [],
                    'fyRangeSel' => $selectedRange,
                    'financialYears' => $financialYears,
                ]);
            }
        } else {
            abort(403, 'Unauthorized role');
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

    // protected function getGroupIcon($groupName)
    // {   
    //     $iconMap = [
    //         // Financial & Banking
    //         'bank' => 'fa-solid fa-building-columns',
    //         'cash' => 'fa-solid fa-money-bill-wave',
    //         'bank accounts' => 'fa-solid fa-building-columns',
    //         'cash-in-hand' => 'fa-solid fa-money-bill-wave',
    //         'current assets' => 'fa-solid fa-chart-line',
    //         'fixed assets' => 'fa-solid fa-industry',
    //         'investments' => 'fa-solid fa-chart-pie',
            
    //         // Sales & Revenue
    //         'sales' => 'fa-solid fa-tags',
    //         'sales accounts' => 'fa-solid fa-tags',
    //         'income' => 'fa-solid fa-money-bill-trend-up',
    //         'revenue' => 'fa-solid fa-money-bill-wave',
            
    //         // Purchases & Expenses
    //         'purchase' => 'fa-solid fa-cart-shopping',
    //         'purchase accounts' => 'fa-solid fa-cart-shopping',
    //         'expenses' => 'fa-solid fa-receipt',
    //         'direct expenses' => 'fa-solid fa-truck',
    //         'indirect expenses' => 'fa-solid fa-file-invoice-dollar',
            
    //         // Debtors & Creditors
    //         'debtors' => 'fa-solid fa-hand-holding-dollar',
    //         'creditors' => 'fa-solid fa-hand-holding-hand',
    //         'sundry debtors' => 'fa-solid fa-hand-holding-dollar',
    //         'sundry creditors' => 'fa-solid fa-hand-holding-hand',
    //         'receivables' => 'fa-solid fa-arrow-down-to-line',
    //         'payables' => 'fa-solid fa-arrow-up-from-line',
            
    //         // Capital & Liabilities
    //         'capital' => 'fa-solid fa-landmark',
    //         'liabilities' => 'fa-solid fa-scale-balanced',
    //         'current liabilities' => 'fa-solid fa-clock-rotate-left',
    //         'long term liabilities' => 'fa-solid fa-calendar-day',
            
    //         // Stock & Inventory
    //         'stock' => 'fa-solid fa-boxes-stacked',
    //         'inventory' => 'fa-solid fa-warehouse',
    //         'stock-in-hand' => 'fa-solid fa-boxes-stacked',
            
    //         // Loans & Advances
    //         'loans' => 'fa-solid fa-hand-holding-dollar',
    //         'advances' => 'fa-solid fa-forward',
    //         'loan' => 'fa-solid fa-hand-holding-dollar',
    //         'advance' => 'fa-solid fa-forward',
            
    //         // Tax
    //         'tax' => 'fa-solid fa-percent',
    //         'duties' => 'fa-solid fa-scale-balanced',
    //         'taxes' => 'fa-solid fa-percent',
            
    //         // General
    //         'accounts' => 'fa-solid fa-book',
    //         'ledger' => 'fa-solid fa-book-open',
    //         'general' => 'fa-solid fa-gear',
    //         'miscellaneous' => 'fa-solid fa-cube',
    //         'profit' => 'fa-solid fa-chart-line',
    //         'loss' => 'fa-solid fa-chart-line-down',
            
    //         // Default fallbacks
    //         'assets' => 'fa-solid fa-chart-line',
    //         'equity' => 'fa-solid fa-scale-balanced',
    //         'revenue' => 'fa-solid fa-money-bill-wave',
    //     ];

    //     $groupName = strtolower(trim($groupName));
        
    //     // Exact match
    //     if (isset($iconMap[$groupName])) {
    //         return $iconMap[$groupName];
    //     }
        
    //     // Partial match
    //     foreach ($iconMap as $key => $icon) {
    //         if (str_contains($groupName, $key)) {
    //             return $icon;
    //         }
    //     }
        
    //     // Default icon based on group type
    //     if (str_contains($groupName, 'asset')) {
    //         return 'fa-solid fa-chart-line';
    //     } elseif (str_contains($groupName, 'liabilit')) {
    //         return 'fa-solid fa-scale-balanced';
    //     } elseif (str_contains($groupName, 'income') || str_contains($groupName, 'revenue')) {
    //         return 'fa-solid fa-money-bill-wave';
    //     } elseif (str_contains($groupName, 'expense') || str_contains($groupName, 'cost')) {
    //         return 'fa-solid fa-receipt';
    //     } elseif (str_contains($groupName, 'capital')) {
    //         return 'fa-solid fa-landmark';
    //     } elseif (str_contains($groupName, 'bank') || str_contains($groupName, 'cash')) {
    //         return 'fa-solid fa-building-columns';
    //     }
        
    //     // Ultimate fallback
    //     return 'fa-solid fa-cube';
    // }

    protected function getGroupIcon($groupName)
    {   
        $iconMap = [
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
            // fallback
            
        ];

        $groupName = strtolower(trim($groupName));

        // Exact match
        if (isset($iconMap[$groupName])) {
            // if($groupName == "loans & advances (asset)"){
            //     dd($iconMap[$groupName]);
            // }
            return asset('assets/images/' . $iconMap[$groupName]);
        }

        // Partial match
        foreach ($iconMap as $key => $icon) {
            if (str_contains($groupName, $key)) {
                return asset('assets/images/' . $icon);
            }
        }

        // Default fallback
        return asset('assets/images/document.png');
    }

    private function clientIds()
    {
        $user = auth()->user();

        if ($user->role == User::ROLES['supervisor']) {
            //return $user->clients()->pluck('id')->toArray();
            $supervisor = Supervisor::find($user->id);   // 👈 cast to Supervisor
            return $supervisor->clients()->pluck('id')->toArray();
        }

        if ($user->role == User::ROLES['manager']) {
            // return Client::whereHas('supervisors', function ($query) use ($user) {
            //     $query->whereIn('id', $user->supervisors()->pluck('id'));
            // })->pluck('id')->toArray();
            $manager = Manager::find($user->id);   // 👈 cast to Manager
            return Client::whereHas('supervisors', function ($query) use ($manager) {
                $query->whereIn('id', $manager->supervisors()->pluck('id'));
            })->pluck('id')->toArray();
        }

        return [];
    }


    private function supervisorClients()
    {
        $user = auth()->user();

        if ($user->role == User::ROLES['supervisor']) {
            $supervisor = Supervisor::find($user->id);  // 👈 get Supervisor model
            return $supervisor
                ->clients()
                ->with(['supervisors', 'dataEntryOperators'])
                ->latest()
                ->get();
        }

        return collect();
    }



    public function saveCardPreferences(Request $request)
    {
        \Log::info('Save preferences request received:', $request->all());

        $validated = $request->validate([
            'selected_groups' => 'required|array',
            'selected_groups.*' => 'integer'
        ]);

        \Log::info('Validated groups:', ['count' => count($validated['selected_groups']), 'groups' => $validated['selected_groups']]);

        // Ensure multiples of 4
        if (count($validated['selected_groups']) % 4 !== 0) {
            \Log::warning('Invalid group count received:', [
                'count' => count($validated['selected_groups']),
                'groups' => $validated['selected_groups']
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Number of selected groups must be a multiple of 4 (currently: ' . count($validated['selected_groups']) . ')'
            ], 422);
        }

        // Ensure all group IDs are integers
        $selectedGroups = array_map('intval', $validated['selected_groups']);

        $preference = UserCardPreference::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'party_id' => auth()->user()->id
            ],
            [
                'selected_groups' => $selectedGroups
            ]
        );

        // Clear session to force reload from database
        $sessionKey = "user_" . auth()->id() . "_selected_groups";
        session()->forget($sessionKey);

        \Log::info('Preferences saved successfully:', [
            'user_id' => auth()->id(),
            'groups_count' => count($selectedGroups),
            'groups' => $selectedGroups
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dashboard preferences saved successfully',
            'groups_count' => count($selectedGroups)
        ]);
    }

    // Get dashboard data
    public function getDashboardData()
    {
        $preference = UserCardPreference::where('user_id', auth()->id())
            ->where('party_id', auth()->user()->party_id)
            ->first();

        $selectedGroupIds = $preference ? $preference->selected_groups : [];

        // Fetch actual group data based on IDs
        $groups = Group::whereIn('id', $selectedGroupIds)->get();

        return response()->json([
            'groups' => $groups,
            'financial_data' => $this->getFinancialData()
        ]);
    }
	
	/**
	 * Get user's selected groups with proper validation
	 */
	private function getUserSelectedGroups($userId, $allGroups, $defaultGroupIds)
	{
		// Get selected groups from database
		$preferences = DB::table('user_card_preferences')
			->where('user_id', $userId)
			->where('party_id', $userId)
			->first();

		if ($preferences && $preferences->selected_groups) {
			$selectedGroups = json_decode($preferences->selected_groups, true);
			// Ensure it's always an array and convert to integers
			if (is_array($selectedGroups)) {
				$selectedGroups = array_map('intval', $selectedGroups);
			} else {
				$selectedGroups = $defaultGroupIds;
			}
		} else {
			$selectedGroups = $defaultGroupIds;
		}

		// Update session for consistency
		$sessionKey = "user_{$userId}_selected_groups";
		session([$sessionKey => $selectedGroups]);

		// Ensure selected groups are valid (exist in user's groups)
		$validSelectedGroups = [];
		foreach ($selectedGroups as $groupId) {
			if ($allGroups->contains('iGroupId', $groupId)) {
				$validSelectedGroups[] = $groupId;
			}
		}

		// If no valid groups found, use defaults
		if (empty($validSelectedGroups)) {
			$validSelectedGroups = $defaultGroupIds;
		}

		return $validSelectedGroups;
	}
}
