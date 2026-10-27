<?php

namespace App\Services\Sms;

class SmsSenderFactory
{
    public static function make(): SmsSender
    {
        return match (strtolower((string) config('sms.driver', 'disabled'))) {
            'http' => new HttpSmsSender((array) config('sms.http', [])),
            'log' => new LogSmsSender(),
            default => new DisabledSmsSender(),
        };
    }
}
