<?php

/*
|--------------------------------------------------------------------------
| Self-registration: Full Name + Email + Mobile, password by email, OTP by SMS
|--------------------------------------------------------------------------
*/

return [

    // One-time code sent by SMS.
    'otp_length' => 6,
    'otp_ttl_minutes' => (int) env('REGISTRATION_OTP_TTL_MINUTES', 10),

    // Wrong codes allowed per OTP before a new one must be requested.
    'otp_max_attempts' => (int) env('REGISTRATION_OTP_MAX_ATTEMPTS', 5),

    // Minimum seconds between two SMS for the same registration.
    'resend_cooldown_seconds' => (int) env('REGISTRATION_RESEND_COOLDOWN', 60),

    // SMS allowed per registration before it is locked for lock_minutes.
    'max_sends' => (int) env('REGISTRATION_MAX_SENDS', 5),
    'lock_minutes' => (int) env('REGISTRATION_LOCK_MINUTES', 30),

    // Unfinished registrations older than this are discarded.
    'request_ttl_hours' => (int) env('REGISTRATION_REQUEST_TTL_HOURS', 24),

    // Generated password e-mailed to the user.
    'password_length' => 12,

    // Per-IP limit for starting registrations (requests per 10 minutes).
    'start_per_ip' => (int) env('REGISTRATION_START_PER_IP', 10),

    // The old API endpoint POST /api/v1/sign_up creates an account without any
    // verification. Keep it on only until the mobile app uses the new
    // /api/v1/sign_up/start + /verify endpoints, then set this to false.
    'legacy_api_signup' => (bool) env('REGISTRATION_LEGACY_API_SIGNUP', true),
];
