<?php
// app/Listeners/LogUserLogin.php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Support\ActivityLogger;

class LogUserLogin
{
    public function handle(Login $event): void
    {
        ActivityLogger::log('login', $event->user);
    }
}
