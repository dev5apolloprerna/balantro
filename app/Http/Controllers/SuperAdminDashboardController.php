<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class SuperAdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('super_admin');
    }

    public function index()
    {
        // $counts = Document::groupBy('status')->selectRaw('status, count(*) as count')->pluck('count', 'status');
        $counts = Cache::remember('super_admin_dashboard:document_counts', now()->addMinutes(5), function () {
            return Document::groupBy('status')->selectRaw('status, count(*) as count')->pluck('count', 'status');
        });
        return view('super_admin.dashboard', [
            'uploaded_count' => $counts['uploaded'] ?? 0,
            'accepted_count' => $counts['accepted'] ?? 0,
            'rejected_count' => $counts['rejected'] ?? 0,
            'data_entry_in_progress_count' => $counts['data_entry_in_progress'] ?? 0,
            'data_entered_count' => $counts['data_entered'] ?? 0,
            'query_raised_count' => $counts['query_raised'] ?? 0,
            'query_resolved_count' => $counts['query_resolved'] ?? 0,
            'completed_count' => $counts['approved'] ?? 0,
            // 'clients' => User::where('role', 'client')->get(),
            // 'managers' => User::where('role', 'manager')->get(),
            // 'supervisors' => User::where('role', 'supervisor')->get(),
            // 'data_entry_operators' => User::where('role', 'data_entry_operator')->get()
            'clients' => Cache::remember('super_admin_dashboard:clients', now()->addMinutes(5), fn () => User::where('role', 'client')->get()),
            'managers' => Cache::remember('super_admin_dashboard:managers', now()->addMinutes(5), fn () => User::where('role', 'manager')->get()),
            'supervisors' => Cache::remember('super_admin_dashboard:supervisors', now()->addMinutes(5), fn () => User::where('role', 'supervisor')->get()),
            'data_entry_operators' => Cache::remember('super_admin_dashboard:data_entry_operators', now()->addMinutes(5), fn () => User::where('role', 'data_entry_operator')->get())
        ]);
    }
}
