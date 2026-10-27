<?php

namespace App\Services\Sms;

interface SmsSender
{
    /**
     * Send a text message to a 10-digit Indian mobile number.
     *
     * @throws SmsDeliveryException when the gateway did not accept the message
     */
    public function send(string $mobile, string $message): void;
}
