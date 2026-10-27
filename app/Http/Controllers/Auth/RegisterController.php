<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Registration\RegistrationException;
use App\Services\Registration\SelfRegistrationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Throwable;

/**
 * Web sign-up: Full Name + Email + Mobile
 *   -> OTP sent by SMS
 *   -> OTP verified -> account created, generated password e-mailed,
 *      user signed in -> dashboard.
 */
class RegisterController extends Controller
{
    use RegistersUsers;

    private const SESSION_KEY = 'registration_request';

    protected $redirectTo = '/home';

    public function __construct(private readonly SelfRegistrationService $registrations)
    {
        $this->middleware('guest');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
        ], [
            'name.required' => 'Please enter your full name.',
            'email.email' => 'Please enter a valid email address.',
            'mobile.required' => 'Please enter your mobile number.',
        ]);

        $mobile = SelfRegistrationService::normaliseMobile($data['mobile']);
        if ($mobile === null) {
            return back()->withInput()->withErrors(['mobile' => 'Please enter a valid 10-digit Indian mobile number.']);
        }

        $ipKey = 'register-start:' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, (int) config('registration.start_per_ip', 10))) {
            $minutes = max(1, (int) ceil(RateLimiter::availableIn($ipKey) / 60));
            return back()->withInput()->withErrors(['email' => "Too many sign-up attempts. Please try again in {$minutes} minutes."]);
        }
        RateLimiter::hit($ipKey, 600);

        // try {
            $start = $this->registrations->start($data['name'], $data['email'], $mobile, $request->ip());
        // } catch (RegistrationException $e) {
        //     return back()->withInput()->withErrors([$e->field === 'otp' ? 'email' : $e->field => $e->getMessage()]);
        // }

        $request->session()->put(self::SESSION_KEY, $start->handle());

        $redirect = redirect()->route('registration.otp.show');
        
        return $start->smsSent
            ? $redirect->with('status', 'We have sent a verification code to ' . SelfRegistrationService::maskMobile($mobile) . '.')
            : $redirect->withErrors(['otp' => 'We could not send the SMS right now. Please tap "Resend code".']);
    }

    public function showOtpForm(Request $request)
    {
        $pending = $this->registrations->find($request->session()->get(self::SESSION_KEY));
        if (! $pending) {
            $request->session()->forget(self::SESSION_KEY);
            return redirect()->route('register')->withErrors(['email' => 'Your sign-up session has expired. Please start again.']);
        }

        return view('auth.verify-registration-otp', [
            'maskedMobile' => SelfRegistrationService::maskMobile($pending->mobile),
            'email' => $pending->email,
            'resendIn' => $this->registrations->secondsUntilResend($pending),
            'otpLength' => (int) config('registration.otp_length', 6),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $length = (int) config('registration.otp_length', 6);
        $request->validate(['otp' => ['required', 'digits:' . $length]], [
            'otp.required' => 'Please enter the verification code.',
            'otp.digits' => "The code must be {$length} digits.",
        ]);

        $pending = $this->registrations->find($request->session()->get(self::SESSION_KEY));
        if (! $pending) {
            $request->session()->forget(self::SESSION_KEY);
            return redirect()->route('register')->withErrors(['email' => 'Your sign-up session has expired. Please start again.']);
        }

        try {
            $done = $this->registrations->verify($pending, (string) $request->input('otp'));
            $user = $done->user;
        } catch (RegistrationException $e) {
            return back()->withErrors(['otp' => $e->getMessage()]);
        }

        $request->session()->forget(self::SESSION_KEY);

        try {
            event(new Registered($user));
            $this->guard()->login($user);
            $request->session()->regenerate();
        } catch (Throwable $e) {
            report($e);
            return redirect()->route('login')->with('status', $done->passwordEmailed
                ? 'Your account has been created. Please sign in with the password we emailed you.'
                : 'Your account has been created. Use "Forgot password" to set your password.');
        }

        $response = redirect($this->redirectPath())->with('notice', $done->passwordEmailed
            ? 'Welcome to Balantro! Your mobile number is verified and your password has been emailed to ' . $user->email . '.'
            : 'Welcome to Balantro! Your mobile number is verified, but we could not email your password. Please set one under Profile > Change password, or use "Forgot password" when signing in.');

        // Same API cookie the normal login sets, so web pages that call the API keep working.
        try {
            $response->withCookie(cookie('jwt', JWTAuth::fromUser($user), (int) config('jwt.ttl', 60), '/', null, $request->isSecure(), true, false, 'Lax'));
        } catch (Throwable $e) {
            Log::warning('Could not issue JWT cookie after sign-up', ['user_id' => $user->id]);
        }

        return $response;
    }

    public function resendOtp(Request $request)
    {
        $pending = $this->registrations->find($request->session()->get(self::SESSION_KEY));
        if (! $pending) {
            $request->session()->forget(self::SESSION_KEY);
            return redirect()->route('register')->withErrors(['email' => 'Your sign-up session has expired. Please start again.']);
        }

        try {
            $this->registrations->resend($pending);
        } catch (RegistrationException $e) {
            return back()->withErrors(['otp' => $e->getMessage()]);
        }

        return back()->with('status', 'A new verification code has been sent to ' . SelfRegistrationService::maskMobile($pending->mobile) . '.');
    }
}
