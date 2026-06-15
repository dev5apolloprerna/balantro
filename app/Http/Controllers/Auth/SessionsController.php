<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Services\DeviceTokenService;

class SessionsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);
        // Handle device info
        if ($request->has('device_token')) {
            $user = Auth::user();
            $deviceTokenService = app(DeviceTokenService::class);
            $deviceTokenService->storeOrUpdateToken(
                $user->id,
                $request->device_token,
                null,
                $deviceTokenService->getDeviceInfo($request->userAgent())
            );
        }
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [];

        if (empty($request->email)) {
            $errors['email'] = __('auth.blank');
        }

        if (empty($request->password)) {
            $errors['password'] = __('auth.blank');
        }

        if (empty($errors) && !Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $errors['email'] = __('auth.failed');
        }

        throw ValidationException::withMessages($errors);
    }

    /**
     * Get the layout to use for client views.
     *
     * @return string
     */
    protected function layout()
    {
        return 'layouts.client';
    }
}