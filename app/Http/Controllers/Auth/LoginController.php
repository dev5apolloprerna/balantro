<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Services\DeviceTokenService;
use App\Models\UserDevice;

class LoginController extends Controller
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
        $this->middleware('auth')->only('logout');
    }

    public function login(Request $request)
    {
        $creds = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($creds, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid credentials']);
        }

        // Issue JWT for API usage and store in httpOnly cookie
        $token   = JWTAuth::fromUser(Auth::user());
        $minutes = config('jwt.ttl', 120); // minutes
        $cookie  = cookie(
            'jwt',
            $token,
            $minutes,
            '/',
            null,
            app()->environment('production'), // secure on prod
            true,  // httpOnly
            false, // raw
            'Lax'  // SameSite (Lax for same-site panels; use 'None' if cross-site + HTTPS)
        );
        // Handle device info
        //$hasDeviceToken = $request->has('device_token');
        //$deviceToken = $request->device_token;

        // if ($request->filled('fcm_token')) {
        //     $user = Auth::user();
        //     UserDevice::where([
        //         'user_id' => $user->id,
        //         'device_type' => $request->device_type,
        //         'browser_name' => $request->browser_name
        //     ])->updateOrCreate(
        //         [
        //             'device_token' => $request->fcm_token,
        //         ],
        //         [
        //             'user_id'      => $user->id,
        //             'device_type'  => $request->device_type ?? 'pc',
        //             'browser_name' => $request->browser_name,
        //             'os_name'      => $request->os_name,
        //             'is_active'    => true,
        //         ]
        //     );
        // }
        
        // if ($request->has('device_token')) {
        //     $user = Auth::user();
        //     $deviceTokenService = app(DeviceTokenService::class);
        //     $deviceTokenService->storeOrUpdateToken(
        //         $user->id,
        //         $request->device_token,
        //         null,
        //         $deviceTokenService->getDeviceInfo($request->userAgent())
        //     );
        // }

        // if ($request->has('device_token')) {
        //     $user = Auth::user();
        //     $deviceTokenService = app(DeviceTokenService::class);
        //     $deviceTokenService->storeOrUpdateToken(
        //         $user->id,
        //         $request->device_token,
        //         null,
        //         $deviceTokenService->getDeviceInfo($request->userAgent())
        //     );
        // }
        

        // Redirect based on role (adjust as you like)
        $home = match (Auth::user()->role) {
            // 'admin'   => 'home',
            // 'manager' => 'home',
            // 'manager' => 'home',
            // 'manager' => 'home',
            default   => 'home',
        };

        return redirect()->intended(route($home))->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        // Optional: invalidate current JWT if blacklist enabled
        try {
            if ($request->cookies->has('jwt')) {
                JWTAuth::setToken($request->cookie('jwt'))->invalidate(true);
            }
        } catch (\Throwable $e) {
        }

        Cookie::queue(Cookie::forget('jwt'));
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
