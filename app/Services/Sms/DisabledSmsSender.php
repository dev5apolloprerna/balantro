<?php

namespace App\Services\Sms;

/** Default driver: refuses to send so nothing pretends an OTP went out. */
class DisabledSmsSender implements SmsSender
{
    public function send(string $mobile, string $message): void
    {
        throw new SmsDeliveryException('SMS is not configured (SMS_DRIVER=disabled).');
    }
}
