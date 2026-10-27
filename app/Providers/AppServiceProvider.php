<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Document;
use App\Observers\DocumentObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // SMS gateway chosen by SMS_DRIVER (see config/sms.php).
        $this->app->bind(\App\Services\Sms\SmsSender::class, fn () => \App\Services\Sms\SmsSenderFactory::make());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Document::observe(DocumentObserver::class);
    }
}
