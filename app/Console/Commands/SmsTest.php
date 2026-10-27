<?php

namespace App\Console\Commands;

use App\Services\Registration\SelfRegistrationService;
use App\Services\Sms\HttpSmsSender;
use App\Services\Sms\SmsDeliveryException;
use App\Services\Sms\SmsSender;
use Illuminate\Console\Command;

/**
 * Sends the registration OTP template with a sample code, to check the SMS
 * gateway settings. Never prints credentials.
 *
 *   php artisan sms:test 98XXXXXXXX
 */
class SmsTest extends Command
{
    protected $signature = 'sms:test {mobile : 10-digit mobile number}';

    protected $description = 'Send a test OTP SMS with the current SMS_* settings';

    public function handle(SmsSender $sms): int
    {
        $mobile = SelfRegistrationService::normaliseMobile((string) $this->argument('mobile'));
        if ($mobile === null) {
            $this->error('Enter a valid 10-digit Indian mobile number.');
            return self::FAILURE;
        }

        $driver = (string) config('sms.driver');
        $url = (string) config('sms.http.url');
        $this->line('Driver: ' . $driver);
        if ($driver === 'http') {
            $parts = parse_url($url) ?: [];
            $this->line('Gateway: ' . ($parts['scheme'] ?? '?') . '://' . ($parts['host'] ?? '?') . ($parts['path'] ?? ''));
            parse_str($parts['query'] ?? '', $query);
            $this->line('Parameters: ' . (implode(', ', array_keys($query)) ?: '(none)'));
            foreach (['{mobile}', '{message}'] as $placeholder) {
                if (! str_contains($url, $placeholder)) {
                    $this->warn("SMS_HTTP_URL has no {$placeholder} placeholder.");
                }
            }
        }

        $message = strtr((string) config('sms.otp_template'), [
            '{otp}' => '123456',
            '{minutes}' => (string) config('registration.otp_ttl_minutes', 10),
        ]);
        $this->line('Message: ' . $message);
        $this->line('To: ' . SelfRegistrationService::maskMobile($mobile));

        try {
            $sms->send($mobile, $message);
            $ok = true;
        } catch (SmsDeliveryException $e) {
            $this->error('Failed: ' . $e->getMessage());
            $ok = false;
        }

        if ($sms instanceof HttpSmsSender && $sms->lastStatus !== null) {
            $this->line('Gateway reply (HTTP ' . $sms->lastStatus . '): ' . $sms->lastResponse);
        }

        if ($ok && $driver === 'log') {
            $this->info('SMS_DRIVER=log: the message was written to storage/logs/laravel.log, not sent.');
        } elseif ($ok) {
            $this->info('Gateway accepted the message. If it does not arrive, check the delivery report in the OnlySMS panel.');
        }

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
