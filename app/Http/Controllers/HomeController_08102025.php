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
        }
        // else if (auth()->user()->role == User::ROLES['client']) {

        //     $documents = $user->documents();

        //     $summary = [
        //         ['key' => '1', 'value' => "Sale & Purchase"],
        //         ['key' => '2', 'value' => "Credit & Debit"],
        //         ['key' => '3', 'value' => "Recepit & Payment"],
        //         ['key' => '4', 'value' => "Cash & Bank balance"]
        //     ];

        //     /*$from = $r->input('from'); // Y-m-d
        //     $to   = $r->input('to');   // Y-m-d

        //     // Optional flags similar to your API
        //     $opts = [
        //         'outflow_negative' => (bool)$r->input('outflow_negative', false),
        //         'groups'           => $r->input('groups'),         // optional override
        //         'exclude_types'    => $r->input('exclude_types'),  // optional override
        //         'date_style'       => $r->input('date_style'),     // for type 4
        //     ];

        //     $graphs = $svc->monthlyGraphs($user->id, $from, $to, $opts); */

        //     $type = (int) $r->input('type', 1);      // keep active tab across reloads
        //     $from = $r->input('from');               // Y-m-d
        //     $to   = $r->input('to');                 // Y-m-d

        //     // Titles per type (for tabs / card title)
        //     $titles = [
        //         1 => 'Sales vs Purchase',
        //         2 => 'Creditors vs Debtors',
        //         3 => 'Receipt vs Payment',
        //         4 => 'Cash & Bank Flow',
        //     ];

        //     // Get monthly series for ALL four types for the selected FY
        //     $charts = [];
        //     $selectedRes = null;
        //     $sum = fn($arr) => array_sum(array_map('floatval', $arr ?? []));

        //     for ($t = 1; $t <= 4; $t++) {
        //         $res = $svc->monthlyGraph($user->id, $from, $to, $t, [
        //             // mirrors your API defaults; override here if needed:
        //             'outflow_negative' => false,
        //             'groups'           => null,
        //             'exclude_types'    => null,
        //             'date_style'       => null,
        //         ]);

        //         if ($t === $type) {
        //             $selectedRes = $res; // for labelFY/range (any type gives same FY/range)
        //         }
        //         //dd($selectedRes);
        //         $charts[] = [
        //             'key'    => $t,
        //             'title'  => $titles[$t],
        //             'months' => $res['months'] ?? [],
        //             'in'     => $res['cashIn']  ?? [],
        //             'out'    => $res['cashOut'] ?? [],
        //             'sumIn'  => $sum($res['cashIn']  ?? []),
        //             'sumOut' => $sum($res['cashOut'] ?? []),
        //         ];
        //     }

        //     // Use selected type (or type 1’s) for header FY label / range / tiles
        //     $basis     = $selectedRes ?: $charts[0] ?? [];
        //     $labelFY   = $selectedRes['fy_label']   ?? ($basis['fy_label']   ?? '');
        //     $range     = $selectedRes['range']      ?? ($basis['range']      ?? ['from' => $from, 'to' => $to]);
        //     $allTotals = $selectedRes['allTotals']  ?? ($basis['allTotals']  ?? []);

        //     $fySel = $r->input('fySel');
        //     return view('home', [
        //         'uploaded_count' => $documents->where('status', 'uploaded')->count(),
        //         'in_progress_count' => $documents->whereIn('status', [
        //             'accepted',
        //             'data_entry_in_progress',
        //             'data_entry_completed',
        //             'query_raised',
        //             'query_resolved'
        //         ])->count(),
        //         'completed_count' => $documents->where('status', 'approved')->count(),
        //         'rejected_count' => $documents->where('status', 'rejected')->count(),
        //         'summary'  => $summary,
        //         /*'charts'   => $graphs['charts'],     // array of 4 charts
        //         'allTotals' => $graphs['allTotals'],  // 8 tiles
        //         'range'    => $graphs['range'],      // ['from','to'] (Y-m-d)
        //         'labelFY'  => $graphs['fy_label'],   // "FY 2024-25" */
        //         'activeType' => $type,
        //         'charts'     => $charts,     // all four series (for instant tab switch)
        //         'labelFY'    => $labelFY,
        //         'range'      => $range,
        //         'allTotals'  => $allTotals,
        //         'fySel' => $fySel
        //     ]);
        // }
        else if (auth()->user()->role == User::ROLES['client']) {

            $user   = auth()->user();
            $userId = (int) $user->id;

            // Summary labels (unchanged)
            $summary = [
                ['key' => '1', 'value' => "Sale & Purchase"],
                ['key' => '2', 'value' => "Credit & Debit"],
                ['key' => '3', 'value' => "Recepit & Payment"],
                ['key' => '4', 'value' => "Cash & Bank balance"]
            ];

            // ---- CALL STORED PROCEDURE ----
            $rows = \DB::select('EXEC dbo.usp_GetClientDocumentSummary ?', [$userId]);
            $row  = $rows[0] ?? (object) [];

            $uploadedCount   = (int) ($row->uploaded_count    ?? 0);
            $inProgressCount = (int) ($row->in_progress_count ?? 0);
            $completedCount  = (int) ($row->completed_count   ?? 0);
            $rejectedCount   = (int) ($row->rejected_count    ?? 0);
            $acceptedCount   = (int) ($row->accepted_count    ?? 0);

            // ---- Chart logic (your existing code, unchanged) ----
            $type = (int) $r->input('type', 1);
            $from = $r->input('from');
            $to   = $r->input('to');

            $titles = [
                1 => 'Sales vs Purchase',
                2 => 'Creditors vs Debtors',
                3 => 'Receipt vs Payment',
                4 => 'Cash & Bank Flow',
            ];

            $charts      = [];
            $selectedRes = null;
            $sum         = fn($arr) => array_sum(array_map('floatval', $arr ?? []));

            for ($t = 1; $t <= 4; $t++) {
                $res = $svc->monthlyGraph($userId, $from, $to, $t, [
                    'outflow_negative' => false,
                    'groups'           => null,
                    'exclude_types'    => null,
                    'date_style'       => null,
                ]);

                if ($t === $type) {
                    $selectedRes = $res;
                }

                $charts[] = [
                    'key'    => $t,
                    'title'  => $titles[$t],
                    'months' => $res['months'] ?? [],
                    'in'     => $res['cashIn']  ?? [],
                    'out'    => $res['cashOut'] ?? [],
                    'sumIn'  => $sum($res['cashIn']  ?? []),
                    'sumOut' => $sum($res['cashOut'] ?? []),
                ];
            }

            $basis     = $selectedRes ?: $charts[0] ?? [];
            $labelFY   = $selectedRes['fy_label']  ?? ($basis['fy_label'] ?? '');
            $range     = $selectedRes['range']     ?? ($basis['range'] ?? ['from' => $from, 'to' => $to]);
            $allTotals = $selectedRes['allTotals'] ?? ($basis['allTotals'] ?? []);
            $fySel     = $r->input('fySel');

            // return view('home', [
            //     // counts from SP
            //     'uploaded_count'    => $uploadedCount,
            //     'in_progress_count' => $inProgressCount,
            //     'completed_count'   => $completedCount,
            //     'rejected_count'    => $rejectedCount,

            //     // tiles + charts
            //     'summary'    => $summary,
            //     'activeType' => $type,
            //     'charts'     => $charts,
            //     'labelFY'    => $labelFY,
            //     'range'      => $range,
            //     'allTotals'  => $allTotals,
            //     'fySel'      => $fySel
            // ]);
            $activeTab = $r->get('tab', 'financial'); // Get the active tab
            if ($activeTab === 'documents') {
                // Return document dashboard view
                return view('home', [
                    'uploaded_count'    => $uploadedCount,
                    'in_progress_count' => $inProgressCount,
                    'completed_count'   => $completedCount,
                    'rejected_count'    => $rejectedCount,
                    'accepted_count' => $acceptedCount,
                    'active_tab'        => 'documents', // Pass active tab to view
                ]);
            } else {
                // Return financial dashboard (your existing code)
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
                    'active_tab'        => 'financial', // Pass active tab to view
                ]);
            }
        } else {
            abort(403, 'Unauthorized role');
        }
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
}
