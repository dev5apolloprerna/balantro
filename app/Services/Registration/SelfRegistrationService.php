<?php

namespace App\Services\Registration;

use App\Mail\RegistrationPasswordMail;
use App\Models\Profile;
use App\Models\RegistrationRequest;
use App\Models\User;
use App\Services\Sms\SmsDeliveryException;
use App\Services\Sms\SmsSender;
use App\Support\Demo\DemoParty;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

/**
 * Self-registration: Full Name + Email + Mobile.
 *
 *   start()   - checks for duplicates, stores a pending request and sends
 *               an SMS OTP. No password exists yet.
 *   resend()  - new OTP, with cooldown and a cap on sends.
 *   verify()  - checks the OTP and only then creates the user (client role)
 *               and profile with a generated password, which is e-mailed
 *               once. The pending request is deleted, so each OTP works once.
 *
 * Only hashes of the password, the OTP and the client handle are stored.
 * Unverified people never get a `users` row, so they cannot log in.
 */
class SelfRegistrationService
{
    public function __construct(private readonly SmsSender $sms)
    {
    }

    /** 10-digit Indian mobile (starts 6-9), accepting +91 / 91 / 0 prefixes and spaces. */
    public static function normaliseMobile(?string $input): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $input) ?? '';

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        } elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        return preg_match('/^[6-9]\d{9}$/', $digits) ? $digits : null;
    }

    /** "+91 98******10" */
    public static function maskMobile(string $mobile): string
    {
        return '+91 ' . substr($mobile, 0, 2) . str_repeat('*', 6) . substr($mobile, -2);
    }

    public function start(string $name, string $email, string $mobile, ?string $ip = null): RegistrationStart
    {
        $name = trim(preg_replace('/\s+/', ' ', $name) ?? $name);
        $email = strtolower(trim($email));

        $this->assertNotRegistered($email, $mobile);
        $this->discardStaleRequests();

        // Limits are checked outside the transaction so a lock they set is kept.
        $previous = RegistrationRequest::query()->where('email', $email)->first();
        if ($previous) {
            $this->assertNotLocked($previous);
            $this->assertCooldownPassed($previous);
            $this->assertSendsRemaining($previous);
        }

        $otp = $this->generateOtp();
        $token = Str::random(40);

        try {
            $request = DB::transaction(function () use ($name, $email, $mobile, $ip, $otp, $token) {
                $existing = RegistrationRequest::query()->where('email', $email)->lockForUpdate()->first();

                $request = $existing ?? new RegistrationRequest(['email' => $email]);
                $request->fill([
                    'name' => $name,
                    'mobile' => $mobile,
                    'token_hash' => hash('sha256', $token),
                    'otp_hash' => Hash::make($otp),
                    'otp_expires_at' => now()->addMinutes($this->otpTtl()),
                    'otp_attempts' => 0,
                    'otp_sends' => ($existing?->otp_sends ?? 0) + 1,
                    'last_sent_at' => now(),
                    'ip' => $ip,
                ]);
                $request->save();

                return $request;
            });
        } catch (QueryException $e) {
            // Two submissions of the same form at the same moment (unique e-mail).
            Log::info('Concurrent registration submit', ['request_email_hash' => hash('sha256', $email)]);
            throw new RegistrationException('Your registration is already being processed. Please wait a moment and try again.', 'email', 'duplicate_submit', 409);
        }

        // OTP by SMS. If this fails the request stays, and the OTP screen
        // offers "Resend code" immediately.
        $smsSent = $this->sendOtpSms($request, $otp);

        return new RegistrationStart($request, $token, $smsSent);
    }

    /** Finds a pending request from the client's "<id>.<token>" handle. */
    public function find(?string $handle): ?RegistrationRequest
    {
        [$id, $token] = array_pad(explode('.', (string) $handle, 2), 2, '');
        if (! ctype_digit($id) || $token === '') {
            return null;
        }

        $request = RegistrationRequest::query()->find((int) $id);
        if (! $request || ! hash_equals($request->token_hash, hash('sha256', $token))) {
            return null;
        }

        if ($request->created_at && $request->created_at->lt(now()->subHours($this->requestTtlHours()))) {
            $request->delete();
            return null;
        }

        return $request;
    }

    public function resend(RegistrationRequest $request): void
    {
        $this->assertNotLocked($request);
        $this->assertCooldownPassed($request);
        $this->assertSendsRemaining($request);

        $otp = $this->generateOtp();
        $request->forceFill([
            'otp_hash' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes($this->otpTtl()),
            'otp_attempts' => 0,
            'otp_sends' => $request->otp_sends + 1,
            'last_sent_at' => now(),
        ])->save();

        if (! $this->sendOtpSms($request, $otp)) {
            throw new RegistrationException('We could not send the SMS. Please try again in a moment.', 'otp', 'sms_failed', 502);
        }
    }

    /**
     * Checks the OTP; on success creates the user with a generated password,
     * e-mails that password and returns the result.
     */
    public function verify(RegistrationRequest $request, string $otp): RegistrationComplete
    {
        $this->assertNotLocked($request);

        if (! $request->otp_hash) {
            throw new RegistrationException('Please request a new verification code.', 'otp', 'otp_missing');
        }

        if ($request->otp_attempts >= $this->maxAttempts()) {
            throw new RegistrationException('Too many incorrect attempts. Please request a new code.', 'otp', 'too_many_attempts', 429);
        }

        if (! $request->otp_expires_at || $request->otp_expires_at->isPast()) {
            throw new RegistrationException('This code has expired. Please request a new code.', 'otp', 'otp_expired');
        }

        if (! Hash::check($otp, $request->otp_hash)) {
            $request->increment('otp_attempts');
            $left = max(0, $this->maxAttempts() - $request->otp_attempts);

            throw new RegistrationException(
                $left > 0
                    ? "The code is incorrect. {$left} attempt" . ($left === 1 ? '' : 's') . ' left.'
                    : 'Too many incorrect attempts. Please request a new code.',
                'otp',
                $left > 0 ? 'otp_invalid' : 'too_many_attempts',
                $left > 0 ? 422 : 429
            );
        }

        $password = $this->generatePassword();
        $name = $request->name;
        $email = $request->email;

        $user = DB::transaction(function () use ($request, $password) {
            // Re-check: someone may have registered this e-mail or mobile meanwhile.
            $this->assertNotRegistered($request->email, $request->mobile);

            $user = new User();
            $user->forceFill([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'role' => User::ROLES['client'],
                'is_active' => true,
            ])->save();

            $profile = ['user_id' => $user->id, 'mobile_no' => $request->mobile];
            if (self::profilesHaveMobileVerifiedAt()) {
                $profile['mobile_verified_at'] = now();
            }
            Profile::query()->forceCreate($profile);

            // Single use: the OTP and the pending data disappear with the request.
            $request->delete();

            return $user;
        });

        // The account exists now; the password is e-mailed once and never stored in plain text.
        try {
            Mail::to($email)->send(new RegistrationPasswordMail($name, $email, $password));
            $emailed = true;
        } catch (Throwable $e) {
            report($e);
            Log::warning('Registration password e-mail failed', ['user_id' => $user->id]);
            $emailed = false;
        }

        return new RegistrationComplete($user, $emailed);
    }

    public function secondsUntilResend(RegistrationRequest $request): int
    {
        if (! $request->last_sent_at) {
            return 0;
        }

        $ready = $request->last_sent_at->copy()->addSeconds($this->cooldown());

        return max(0, (int) ceil(now()->diffInSeconds($ready, false)));
    }

    // ---------------------------------------------------------------------

    private function sendOtpSms(RegistrationRequest $request, string $otp): bool
    {
        $message = strtr((string) config('sms.otp_template'), [
            '{otp}' => $otp,
            '{minutes}' => (string) $this->otpTtl(),
        ]);

        try {
            $this->sms->send($request->mobile, $message);
            return true;
        } catch (SmsDeliveryException $e) {
            Log::warning('Registration OTP SMS failed: ' . $e->getMessage(), ['registration_request_id' => $request->id]);
            // Let the user press "Resend code" straight away.
            $request->forceFill(['last_sent_at' => null, 'otp_sends' => max(0, $request->otp_sends - 1)])->save();
            return false;
        }
    }

    private function assertNotRegistered(string $email, string $mobile): void
    {
        if (DemoParty::isDemoEmail($email) || User::withTrashed()->where('email', $email)->exists()) {
            throw new RegistrationException('This email address is already registered. Please sign in instead.', 'email', 'email_taken');
        }

        if (Profile::query()->where('mobile_no', $mobile)->exists()) {
            throw new RegistrationException('This mobile number is already registered. Please sign in instead.', 'mobile', 'mobile_taken');
        }
    }

    private function assertNotLocked(RegistrationRequest $request): void
    {
        if ($request->locked_until && $request->locked_until->isFuture()) {
            $minutes = max(1, (int) ceil(now()->diffInSeconds($request->locked_until) / 60));
            throw new RegistrationException("Too many attempts. Please try again in {$minutes} minutes.", 'otp', 'locked', 429);
        }
    }

    private function assertCooldownPassed(RegistrationRequest $request): void
    {
        $wait = $this->secondsUntilResend($request);
        if ($wait > 0) {
            throw new RegistrationException("Please wait {$wait} seconds before requesting another code.", 'otp', 'cooldown', 429);
        }
    }

    private function assertSendsRemaining(RegistrationRequest $request): void
    {
        if ($request->otp_sends >= (int) config('registration.max_sends', 5)) {
            $request->forceFill([
                'locked_until' => now()->addMinutes((int) config('registration.lock_minutes', 30)),
                'otp_sends' => 0,
                'otp_hash' => null,
            ])->save();

            throw new RegistrationException(
                'Too many codes requested. Please try again in ' . (int) config('registration.lock_minutes', 30) . ' minutes.',
                'otp',
                'locked',
                429
            );
        }
    }

    private function discardStaleRequests(): void
    {
        RegistrationRequest::query()
            ->where('created_at', '<', now()->subHours($this->requestTtlHours()))
            ->delete();
    }

    private function generateOtp(): string
    {
        $length = max(4, (int) config('registration.otp_length', 6));

        return str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    /** Cryptographically random; at least one upper, lower, digit and symbol; no look-alike characters. */
    private function generatePassword(): string
    {
        $sets = [
            'ABCDEFGHJKLMNPQRSTUVWXYZ',
            'abcdefghijkmnpqrstuvwxyz',
            '23456789',
            '@#$%&*!?',
        ];
        $length = max(10, (int) config('registration.password_length', 12));

        $chars = [];
        foreach ($sets as $set) {
            $chars[] = $set[random_int(0, strlen($set) - 1)];
        }
        $all = implode('', $sets);
        while (count($chars) < $length) {
            $chars[] = $all[random_int(0, strlen($all) - 1)];
        }

        for ($i = count($chars) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
        }

        return implode('', $chars);
    }

    private static function profilesHaveMobileVerifiedAt(): bool
    {
        static $has = null;

        return $has ??= Schema::hasColumn('profiles', 'mobile_verified_at');
    }

    private function otpTtl(): int
    {
        return max(1, (int) config('registration.otp_ttl_minutes', 10));
    }

    private function maxAttempts(): int
    {
        return max(1, (int) config('registration.otp_max_attempts', 5));
    }

    private function cooldown(): int
    {
        return max(0, (int) config('registration.resend_cooldown_seconds', 60));
    }

    private function requestTtlHours(): int
    {
        return max(1, (int) config('registration.request_ttl_hours', 24));
    }
}
