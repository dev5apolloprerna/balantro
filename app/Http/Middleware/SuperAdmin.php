<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return redirect('/')->with('error', __('admin.base.flash.authorize_super_admin_alert'));
        }

        return $next($request);
    }
}
