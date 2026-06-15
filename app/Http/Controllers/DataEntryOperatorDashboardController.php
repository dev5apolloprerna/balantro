<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DataEntryOperatorDashboardController extends Controller
{
    public function __construct()
    {
        // If you use Spatie Permissions, this line is enough:
        // $this->middleware('role:data_entry_operator');

        // Or use a custom middleware (see section 2):
        $this->middleware('ensure.data.entry.operator');
    }

    /**
     * GET /data-entry-operator
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Eager-load clients with managers & supervisors
        $clients = $user->clients()
            ->with(['managers', 'supervisors'])
            ->orderByDesc('created_at')
            ->get();

        // Document counts by status for the user's clients
        $counts = $this->documentCountsForClientIds($user->clients()->pluck('id'));

        // Map counts to view vars (defaults to 0)
        $uploadedCount              = $counts['uploaded']              ?? 0;
        $acceptedCount              = $counts['accepted']              ?? 0;
        $rejectedCount              = $counts['rejected']              ?? 0;
        $dataEntryInProgressCount   = $counts['data_entry_in_progress'] ?? 0;
        $dataEnteredCount           = $counts['data_entered']          ?? 0;
        $queryRaisedCount           = $counts['query_raised']          ?? 0;
        $queryResolvedCount         = $counts['query_resolved']        ?? 0;
        $completedCount             = $counts['approved']              ?? 0; // Rails mapped "approved" to completed

        // Render your Blade that extends the "data_entry_operator" layout
        return view('data_entry_operator.dashboard.index', [
            'clients'                     => $clients,
            'uploaded_count'              => $uploadedCount,
            'accepted_count'              => $acceptedCount,
            'rejected_count'              => $rejectedCount,
            'data_entry_in_progress_count' => $dataEntryInProgressCount,
            'data_entered_count'          => $dataEnteredCount,
            'query_raised_count'          => $queryRaisedCount,
            'query_resolved_count'        => $queryResolvedCount,
            'completed_count'             => $completedCount,
        ]);
    }

    /**
     * Build a status => count array for documents belonging to given client IDs.
     */
    protected function documentCountsForClientIds($clientIds)
    {
        if ($clientIds->isEmpty()) {
            return [];
        }

        // If you have relations like Document->fileAttachment() and ->user(),
        // eager-load them similarly to Rails includes.
        $rows = Document::query()
            ->with(['fileAttachment', 'user'])
            ->whereIn('user_id', $clientIds)
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status'); // returns key => value

        return $rows->toArray();
    }
}
