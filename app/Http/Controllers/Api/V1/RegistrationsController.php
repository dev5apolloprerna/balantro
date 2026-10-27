<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Registration\RegistrationException;
use App\Services\Registration\SelfRegistrationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Mobile-app sign-up.
 *
 * New flow (same as the web):
 *   POST /api/v1/sign_up/start   name, email, mobile -> registration_token
 *   POST /api/v1/sign_up/verify  registration_token, otp -> user + JWT
 *   POST /api/v1/sign_up/resend  registration_token
 *
 * Old flow: POST /api/v1/sign_up (name, email, password) creates an account
 * with no verification. Controlled by REGISTRATION_LEGACY_API_SIGNUP; turn it
 * off once the app uses the new endpoints.
 */
class RegistrationsController extends Controller
{
    use \App\Http\Concerns\ResponseConcern;

    public function __construct(private readonly SelfRegistrationService $registrations)
    {
    }

    public function start(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $mobile = SelfRegistrationService::normaliseMobile($request->input('mobile'));
        if ($mobile === null) {
            return response()->json(['status' => 422, 'errors' => ['mobile' => ['Please enter a valid 10-digit Indian mobile number.']]], 422);
        }

        try {
            $start = $this->registrations->start($request->input('name'), $request->input('email'), $mobile, $request->ip());
        } catch (RegistrationException $e) {
            return $this->failure($e);
        }

        return response()->json([
            'status' => 200,
            'message' => $start->smsSent
                ? 'Verification code sent by SMS.'
                : 'We could not send the SMS; call sign_up/resend.',
            'sms_sent' => $start->smsSent,
            'registration_token' => $start->handle(),
            'mobile_masked' => SelfRegistrationService::maskMobile($mobile),
            'otp_expires_in' => (int) config('registration.otp_ttl_minutes', 10) * 60,
            'resend_after' => $this->registrations->secondsUntilResend($start->request),
        ], 200);
    }

    public function verify(Request $request): JsonResponse
    {
        $length = (int) config('registration.otp_length', 6);
        $validator = Validator::make($request->all(), [
            'registration_token' => ['required', 'string', 'max:100'],
            'otp' => ['required', 'digits:' . $length],
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $pending = $this->registrations->find($request->input('registration_token'));
        if (! $pending) {
            return response()->json(['status' => 410, 'message' => 'This sign-up has expired. Please start again.'], 410);
        }

        try {
            $done = $this->registrations->verify($pending, (string) $request->input('otp'));
            $user = $done->user;
        } catch (RegistrationException $e) {
            return $this->failure($e);
        }

        event(new Registered($user));
        $token = auth('api')->login($user);

        return response()->json([
            'status' => 200,
            'message' => $done->passwordEmailed
                ? 'Signed up successfully. Your password has been emailed.'
                : 'Signed up successfully, but the password email could not be sent. Use forgot password to set one.',
            'password_emailed' => $done->passwordEmailed,
            'user' => $user->fresh(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ], 200);
    }

    public function resend(Request $request): JsonResponse
    {
        $pending = $this->registrations->find((string) $request->input('registration_token'));
        if (! $pending) {
            return response()->json(['status' => 410, 'message' => 'This sign-up has expired. Please start again.'], 410);
        }

        try {
            $this->registrations->resend($pending);
        } catch (RegistrationException $e) {
            return $this->failure($e);
        }

        return response()->json([
            'status' => 200,
            'message' => 'A new verification code has been sent.',
            'mobile_masked' => SelfRegistrationService::maskMobile($pending->mobile),
            'resend_after' => $this->registrations->secondsUntilResend($pending->fresh()),
        ], 200);
    }

    /** Old one-step sign-up (no verification). Kept only while older app versions need it. */
    public function register(Request $request)
    {
        if (! config('registration.legacy_api_signup', true)) {
            return response()->json([
                'status' => 410,
                'message' => 'Please update the app. Sign-up now requires mobile verification (sign_up/start and sign_up/verify).',
            ], 410);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => User::ROLES['client'],
            'password' => Hash::make($request->password),
            'confirmation_token' => Str::uuid()
        ]);

        $user->profile()->create([]);

        // The API guard issues the token (the default web guard returns nothing).
        $token = auth('api')->login($user);

        return response()->json([
            'message' => 'Signed up successfully',
            'user' => $user,
            'token' => $token,
            'status' => 200
        ], 200);
    }

    private function failure(RegistrationException $e): JsonResponse
    {
        return response()->json([
            'status' => $e->status,
            'reason' => $e->reason,
            'message' => $e->getMessage(),
            'errors' => [$e->field => [$e->getMessage()]],
        ], $e->status);
    }
}
