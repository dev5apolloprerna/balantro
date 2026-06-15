<?php

namespace App\Providers;

use App\Services\Firebase\PushNotificationService;
use Illuminate\Support\ServiceProvider;

class FirebaseServiceProvider extends ServiceProvider
{
    // public function register()
    // {
    //     $this->app->singleton(PushNotificationService::class, function ($app) {
    //         return new PushNotificationService();
    //     });
    // }

    public function register()
    {
        $this->app->singleton(FirebaseNotificationService::class, function ($app) {
            return new FirebaseNotificationService();
        });
    }
}