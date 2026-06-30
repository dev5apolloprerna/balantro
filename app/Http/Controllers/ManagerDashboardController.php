<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\DataEntryOperator;
use App\Models\Document;
use App\Models\Supervisor;
use App\Services\ManagerDocumentsService;
use Illuminate\Support\Facades\Cache;

class ManagerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('manager');
    }

    public function index(ManagerDocumentsService $documentService)
    {
        // $counts = $documentService->getGroupedByStatus();
        $user = auth()->user();
        $managerId = (int) $user->id;
        $supervisorIds = $user->supervisors->pluck('id');
        $supervisorCacheKey = $supervisorIds->sort()->implode(',');

        $counts = Cache::remember("manager_dashboard:{$managerId}:document_counts", now()->addMinutes(5), function () use ($documentService) {
            return $documentService->getGroupedByStatus();
        });

        return view('manager.dashboard', [
            'uploaded_count' => $counts['uploaded'] ?? 0,
            'accepted_count' => $counts['accepted'] ?? 0,
            'rejected_count' => $counts['rejected'] ?? 0,
            'data_entry_in_progress_count' => $counts['data_entry_in_progress'] ?? 0,
            'data_entered_count' => $counts['data_entered'] ?? 0,
            'query_raised_count' => $counts['query_raised'] ?? 0,
            'query_resolved_count' => $counts['query_resolved'] ?? 0,
            'completed_count' => $counts['approved'] ?? 0,
            // 'clients' => Client::whereHas('supervisors', function($query) use ($user) {
            //     $query->whereIn('id', $user->supervisors->pluck('id'));
            // })->with(['supervisors', 'dataEntryOperators'])->latest()->get(),
            'clients' => Cache::remember("manager_dashboard:{$managerId}:clients:{$supervisorCacheKey}", now()->addMinutes(5), function () use ($supervisorIds) {
                return Client::whereHas('supervisors', function ($query) use ($supervisorIds) {
                    $query->whereIn('id', $supervisorIds);
                })->with(['supervisors', 'dataEntryOperators'])->latest()->get();
            }),
            'supervisors' => $user->supervisors,
            // 'data_entry_operators' => DataEntryOperator::whereHas('managers', function($query) use ($user) {
            //     $query->where('id', $user->id);
            // })->orWhereHas('supervisors', function($query) use ($user) {
            //     $query->whereIn('id', $user->supervisors->pluck('id'));
            // })->with(['managers', 'supervisors'])->latest()->get()
            'data_entry_operators' => Cache::remember("manager_dashboard:{$managerId}:data_entry_operators:{$supervisorCacheKey}", now()->addMinutes(5), function () use ($managerId, $supervisorIds) {
                return DataEntryOperator::whereHas('managers', function ($query) use ($managerId) {
                    $query->where('id', $managerId);
                })->orWhereHas('supervisors', function ($query) use ($supervisorIds) {
                    $query->whereIn('id', $supervisorIds);
                })->with(['managers', 'supervisors'])->latest()->get();
            })
        ]);
    }
}