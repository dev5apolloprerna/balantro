<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\DataEntryOperator;
use App\Models\Document;
use App\Models\Supervisor;
use App\Services\ManagerDocumentsService;

class ManagerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('manager');
    }

    public function index(ManagerDocumentsService $documentService)
    {
        $counts = $documentService->getGroupedByStatus();
        $user = auth()->user();

        return view('manager.dashboard', [
            'uploaded_count' => $counts['uploaded'] ?? 0,
            'accepted_count' => $counts['accepted'] ?? 0,
            'rejected_count' => $counts['rejected'] ?? 0,
            'data_entry_in_progress_count' => $counts['data_entry_in_progress'] ?? 0,
            'data_entered_count' => $counts['data_entered'] ?? 0,
            'query_raised_count' => $counts['query_raised'] ?? 0,
            'query_resolved_count' => $counts['query_resolved'] ?? 0,
            'completed_count' => $counts['approved'] ?? 0,
            'clients' => Client::whereHas('supervisors', function($query) use ($user) {
                $query->whereIn('id', $user->supervisors->pluck('id'));
            })->with(['supervisors', 'dataEntryOperators'])->latest()->get(),
            'supervisors' => $user->supervisors,
            'data_entry_operators' => DataEntryOperator::whereHas('managers', function($query) use ($user) {
                $query->where('id', $user->id);
            })->orWhereHas('supervisors', function($query) use ($user) {
                $query->whereIn('id', $user->supervisors->pluck('id'));
            })->with(['managers', 'supervisors'])->latest()->get()
        ]);
    }
}