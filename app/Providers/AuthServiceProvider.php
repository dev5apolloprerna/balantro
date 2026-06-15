<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate::define('super-admin', fn($user) => $user->isSuperAdmin());
        // Gate::define('admin', fn($user) => $user->isAdmin() || $user->isSuperAdmin());
        $this->registerPolicies();

        // 🔓 Superadmin bypass — grants EVERYTHING without defining abilities
        Gate::before(function ($user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        // (Optional) define dynamic gates from the DB so non-superadmins work too
        try {
            \App\Models\Permission::query()->select('action', 'subject')->get()
                ->each(function ($p) {
                    $ability = "{$p->subject}.{$p->action}"; // e.g. managers.create
                    Gate::define($ability, fn($user) => $user->hasPermission($p->action, $p->subject));
                });
        } catch (\Throwable $e) {
            // ignore during early migrations
        }
    }
}
