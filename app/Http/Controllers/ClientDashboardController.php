<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('client');
    }

    public function index()
    {
        $user = auth()->user();
        $documents = $user->documents();

        return view('client.dashboard', [
            'uploaded_count' => $documents->where('status', 'uploaded')->count(),
            'in_progress_count' => $documents->whereIn('status', [
                'accepted', 
                'data_entry_in_progress', 
                'data_entry_completed', 
                'query_raised', 
                'query_resolved'
            ])->count(),
            'completed_count' => $documents->where('status', 'approved')->count(),
            'rejected_count' => $documents->where('status', 'rejected')->count()
        ]);
    }
}