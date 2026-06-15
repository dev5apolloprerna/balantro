<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if ($this->shouldCheckAccess()) {
                $this->hasAccess();
            }
            return $next($request);
        });
    }

    protected function shouldCheckAccess()
    {
        return Auth::check() && !$this->isDeviseController();
    }

    protected function isDeviseController()
    {
        return $this instanceof \App\Http\Controllers\Auth\RegistrationsController ||
               $this instanceof \App\Http\Controllers\Auth\SessionsController ||
               $this instanceof \App\Http\Controllers\Auth\PasswordsController ||
               $this instanceof \App\Http\Controllers\Auth\ConfirmationsController;
    }

    protected function hasAccess()
    {
        try {
            $this->authorize($this->getActionName(), static::class);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return view('shared.access_denied')->with('status', 403);
        }
    }

    protected function getActionName()
    {
        return request()->route()->getActionMethod();
    }

    public function afterSignInPath()
    {
        $user = Auth::user();
        
        switch ($user->role) {
            case 'super_admin':
                return route('super_admin.dashboard');
            case 'manager':
                return route('manager.dashboard');
            case 'supervisor':
                return route('supervisor.dashboard');
            case 'data_entry_operator':
                return route('data_entry_operator.dashboard');
            case 'client':
                return $user->profile ? route('client.dashboard') : route('profiles.create', ['first_visit' => true]);
            default:
                return route('home');
        }
    }
}