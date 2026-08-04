<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationOtpMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Validate the registration and send an OTP before creating the account.
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $pending = [
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->lower()->toString(),
            'password' => Crypt::encryptString($request->string('password')->toString()),
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10)->timestamp,
            'attempts' => 0,
            'last_sent_at' => now()->timestamp,
        ];
        try {
            Mail::to($pending['email'])->send(new RegistrationOtpMail($pending['name'], $otp));
        } catch (\Throwable $exception) {
            report($exception);
             return back()->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors([
                    'email' => 'We could not send the verification code. Please check the mail configuration and try again.',
                ]);
        }

        // Do not retain credentials unless the OTP was accepted by the mail transport.
        $request->session()->put('registration', $pending);

        return redirect()->route('registration.otp.show')
            ->with('status', 'We sent a 6-digit verification code to your email address.');
    }

    public function showOtpForm(Request $request)
    {
        abort_unless($request->session()->has('registration'), 404);

        return view('auth.verify-registration-otp', [
            'email' => $request->session()->get('registration.email'),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => ['required', 'digits:6']]);

        $pending = $request->session()->get('registration');
        if (! $pending) {
            return redirect()->route('register')->withErrors([
                'email' => 'Your registration session has expired. Please register again.',
            ]);
        }

        if (now()->timestamp > $pending['expires_at']) {
            $request->session()->forget('registration');

            return redirect()->route('register')->withErrors([
                'email' => 'The verification code has expired. Please register again.',
            ]);
        }

        if ($pending['attempts'] >= 5) {
            $request->session()->forget('registration');

            return redirect()->route('register')->withErrors([
                'email' => 'Too many incorrect attempts. Please register again.',
            ]);
        }

        if (! Hash::check($request->otp, $pending['otp_hash'])) {
            $pending['attempts']++;
            $request->session()->put('registration', $pending);

            return back()->withErrors(['otp' => 'The verification code is incorrect.']);
        }

        Validator::make($pending, [
            'email' => ['required', 'email', 'unique:users,email'],
        ])->validate();

        $user = $this->create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'password' => Crypt::decryptString($pending['password']),
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();
        event(new Registered($user));
        $this->guard()->login($user);
        $request->session()->forget('registration');

        return $this->registered($request, $user) ?: redirect($this->redirectPath());
    }

    public function resendOtp(Request $request)
    {
        $pending = $request->session()->get('registration');
        if (! $pending) {
            return redirect()->route('register');
        }

        if (now()->timestamp - $pending['last_sent_at'] < 60) {
            return back()->withErrors(['otp' => 'Please wait 60 seconds before requesting another code.']);
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $pending['otp_hash'] = Hash::make($otp);
        $pending['expires_at'] = now()->addMinutes(10)->timestamp;
        $pending['attempts'] = 0;
        $pending['last_sent_at'] = now()->timestamp;
        // $request->session()->put('registration', $pending);
         try {
            Mail::to($pending['email'])->send(new RegistrationOtpMail($pending['name'], $otp));
        } catch (\Throwable $exception) {
            report($exception);

        return back()->withErrors([
                'otp' => 'We could not resend the verification code. Please check the mail configuration and try again.',
            ]);
        }

        // Keep the previous code valid if delivery fails.
        $request->session()->put('registration', $pending);

        return back()->with('status', 'A new verification code has been sent.');
    }
}
