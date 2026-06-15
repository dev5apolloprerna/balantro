<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LandingController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except(['index']);
    }

    public function index()
    {
        dd("Hi");
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }

        return view('landing.index');
    }

    protected function redirectToDashboard()
    {
        $user = Auth::user();
        
        // Implement your role-based redirection logic here
        switch ($user->role) {
            case 'super_admin':
                return redirect()->route('super_admin_dashboard');
            case 'manager':
                return redirect()->route('manager_dashboard');
            case 'supervisor':
                return redirect()->route('supervisor_dashboard');
            case 'data_entry_operator':
                return redirect()->route('data_entry_operator_dashboard');
            case 'client':
                return redirect()->route('client_dashboard');
            default:
                return redirect('/home');
        }
    }
}