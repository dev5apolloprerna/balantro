<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $user = auth()->user();

        // Use your role map from User model
        if ($user->role !== \App\Models\User::ROLES[$role]) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
