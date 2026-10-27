<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Generic HTTP gateway driver (OnlySMS and most Indian bulk-SMS panels).
 *
 * The URL comes from SMS_HTTP_URL with placeholders; credentials come from
 * their own environment variables. The full URL is never logged because it
 * contains credentials and the OTP.
 */
class HttpSmsSender implements SmsSender
{
    /** Status and redacted body of the last gateway reply (for `php artisan sms:test`). */
    public ?int $lastStatus = null;
    public ?string $lastResponse = null;

    public function __construct(private readonly array $config)
    {
    }

    public function send(string $mobile, string $message): void
    {
        $template = (string) ($this->config['url'] ?? '');
        if ($template === '') {
            throw new SmsDeliveryException('SMS_HTTP_URL is not set.');
        }
        foreach (['{mobile}', '{message}'] as $required) {
            if (! str_contains($template, $required)) {
                throw new SmsDeliveryException("SMS_HTTP_URL must contain {$required}. Use the send-SMS API link from the gateway panel, not the login page.");
            }
        }

        $values = [
            '{username}' => (string) ($this->config['username'] ?? ''),
            '{password}' => (string) ($this->config['password'] ?? ''),
            '{api_key}' => (string) ($this->config['api_key'] ?? ''),
            '{sender}' => (string) ($this->config['sender'] ?? ''),
            '{template_id}' => (string) ($this->config['template_id'] ?? ''),
            '{entity_id}' => (string) ($this->config['entity_id'] ?? ''),
            '{mobile}' => ($this->config['mobile_prefix'] ?? '') . $mobile,
            '{message}' => $message,
        ];

        $url = strtr($template, array_map('rawurlencode', $values));
        $method = strtoupper((string) ($this->config['method'] ?? 'GET'));
        $timeout = max(1, (int) ($this->config['timeout'] ?? 15));

        try {
            $client = Http::timeout($timeout)->connectTimeout(min(5, $timeout));

            if ($method === 'POST') {
                [$endpoint, $query] = array_pad(explode('?', $url, 2), 2, '');
                parse_str($query, $form);
                $response = $client->asForm()->post($endpoint, $form);
            } else {
                $response = $client->get($url);
            }
        } catch (Throwable $e) {
            Log::warning('SMS gateway request failed', ['mobile' => self::mask($mobile), 'error' => get_class($e)]);
            throw new SmsDeliveryException('Could not reach the SMS gateway.');
        }

        $body = (string) $response->body();
        $this->lastStatus = $response->status();
        $this->lastResponse = $this->redact(mb_substr(trim($body), 0, 500));

        // An HTML page means the URL is a website page (e.g. the login page), not the API.
        if (preg_match('/<\s*(!doctype|html|body|form)\b/i', $body)) {
            Log::warning('SMS gateway returned a web page instead of an API reply; check SMS_HTTP_URL', [
                'mobile' => self::mask($mobile),
                'status' => $response->status(),
            ]);
            throw new SmsDeliveryException('The SMS gateway returned a web page, not an API reply. SMS_HTTP_URL is probably the login page.');
        }

        $failureMarker = trim((string) ($this->config['failure_contains'] ?? ''));
        $successMarker = trim((string) ($this->config['success_contains'] ?? ''));

        $failed = ! $response->successful()
            || ($failureMarker !== '' && stripos($body, $failureMarker) !== false)
            || ($successMarker !== '' && stripos($body, $successMarker) === false);

        if ($failed) {
            Log::warning('SMS gateway rejected the message', [
                'mobile' => self::mask($mobile),
                'status' => $response->status(),
                'response' => $this->redact(mb_substr($body, 0, 300)),
            ]);
            throw new SmsDeliveryException('The SMS gateway did not accept the message.');
        }

        Log::info('SMS accepted by gateway', ['mobile' => self::mask($mobile), 'status' => $response->status()]);
    }

    public static function mask(string $mobile): string
    {
        $digits = preg_replace('/\D/', '', $mobile) ?? '';

        return strlen($digits) >= 4
            ? substr($digits, 0, 2) . str_repeat('*', max(0, strlen($digits) - 4)) . substr($digits, -2)
            : '****';
    }

    /** Remove credentials from a gateway response before logging it. */
    private function redact(string $text): string
    {
        foreach (['password', 'api_key'] as $key) {
            $secret = (string) ($this->config[$key] ?? '');
            if ($secret !== '') {
                $text = str_replace($secret, '[redacted]', $text);
            }
        }

        // A gateway might echo the message back; never let an OTP reach the log.
        return preg_replace('/\b\d{4,8}\b/', '[digits]', $text) ?? $text;
    }
}
