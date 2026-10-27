<?php

/*
|--------------------------------------------------------------------------
| SMS gateway
|--------------------------------------------------------------------------
|
| Drivers:
|   disabled - default. Every send fails with "SMS is not configured", so
|              registration never pretends an OTP was sent.
|   log      - development only. Writes the message to the log. Refused when
|              APP_ENV=production. Never use it on a server real users reach.
|   http     - any HTTP gateway (OnlySMS, MSG91, Fast2SMS, ...). Copy the
|              "Send SMS" API URL from the provider's panel into SMS_HTTP_URL
|              and replace the values with these placeholders:
|
|                {username} {password} {api_key} {sender} {mobile}
|                {message}  {template_id} {entity_id}
|
|              Example (the parameter names come from your provider):
|              SMS_HTTP_URL="http://onlysms.in/api/sendhttp.php?user={username}&password={password}&sender={sender}&mobile={mobile}&message={message}&templateid={template_id}"
|
| Indian DLT rules: the message text must match the template registered on
| DLT exactly (only the variable part changes), and the sender ID and
| template ID must be the approved ones.
*/

return [

    'driver' => env('SMS_DRIVER', 'disabled'),

    'http' => [
        'url' => env('SMS_HTTP_URL'),
        'method' => strtoupper((string) env('SMS_HTTP_METHOD', 'GET')),
        'timeout' => (int) env('SMS_HTTP_TIMEOUT', 15),

        'username' => env('SMS_USERNAME'),
        'password' => env('SMS_PASSWORD'),
        'api_key' => env('SMS_API_KEY'),
        'sender' => env('SMS_SENDER_ID'),
        'template_id' => env('SMS_DLT_TEMPLATE_ID'),
        'entity_id' => env('SMS_DLT_ENTITY_ID'),

        // Prefix added to the 10-digit mobile number, e.g. "91" if the gateway wants 91XXXXXXXXXX.
        'mobile_prefix' => env('SMS_MOBILE_PREFIX', ''),

        // Optional checks on the gateway's response body (case-insensitive).
        // A 2xx response counts as sent unless failure_contains matches, or
        // success_contains is set and does not match.
        'success_contains' => env('SMS_HTTP_SUCCESS_CONTAINS'),
        'failure_contains' => env('SMS_HTTP_FAILURE_CONTAINS', 'error'),
    ],

    // Must match the DLT-approved template text. {otp} and {minutes} are filled in.
    'otp_template' => env(
        'SMS_OTP_TEMPLATE',
        '{otp} is your Balantro verification code. It is valid for {minutes} minutes. Do not share it with anyone.'
    ),
];
