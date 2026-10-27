<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

/**
 * Development only: writes the SMS to the log instead of sending it, so the
 * OTP can be read from storage/logs while testing. Refused in production.
 */
class LogSmsSender implements SmsSender
{
    public function send(string $mobile, string $message): void
    {
        if (app()->environment('production')) {
            throw new SmsDeliveryException('The log SMS driver cannot be used in production.');
        }

        Log::info('[SMS log driver - development only] SMS to ' . $mobile . ': ' . $message);
    }
}
