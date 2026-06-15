<?php
// app/Listeners/LogUserLogout.php
namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Support\ActivityLogger;

class LogUserLogout
{
    public function handle(Logout $event): void
    {
        ActivityLogger::log('logout', $event->user);
    }
}
