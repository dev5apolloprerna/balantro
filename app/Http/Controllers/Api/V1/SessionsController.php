<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Services\DeviceTokenService;
use App\Models\UserDevice;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SessionsController extends Controller
{
    public function login(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status' => 401,
                'error' => 'Invalid email or password'
            ], 401);
        }
        $user = auth('api')->user();
        if ($user->type == "Client") {

            auth('api')->logout();

            return response()->json([
                'status' => 403,
                'error' => 'Client users are not allowed to login.'
            ], 403);
        }
        // Handle device info
        // if ($request->has('user_device')) {
        //     auth('api')->user()->devices()->updateOrCreate(
        //         ['device_token' => $request->user_device['device_token']],
        //         $request->user_device
        //     );
        // }
        // Handle device info
        
        /*if ($request->has('device_token')) {
            $user = Auth::user();
            $deviceTokenService = app(DeviceTokenService::class);
            $deviceTokenService->storeOrUpdateToken(
                $user->id,
                $request->device_token,
                null,
                $deviceTokenService->getDeviceInfo($request->userAgent())
            );
        }*/
        // if ($request->filled('fcm_token')) {
                
            UserDevice::where([
                'user_id' => auth('api')->user()->id,
                'device_type' => $request->device_type ?? "",
                'browser_name' => $request->browser_name ?? ""
            ])->updateOrCreate(
                [
                    'device_token' => $request->fcm_token  ?? "",
                ],
                [
                    'user_id'      => auth('api')->user()->id,
                    'device_type'  => $request->device_type ?? 'pc',
                    'browser_name' => $request->browser_name,
                    'os_name'      => $request->os_name,
                    'is_active'    => true,
                ]
            );
        // }

        return response()->json([
            'status' => 200,
            'message' => 'Login successful',
            'user' => auth('api')->user(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ], 200);
    }


    public function destroy(Request $request)
    {
        $user = auth('api')->user();

        if ($user) {
            if ($request->has('device_token')) {
                $user->devices()
                    ->where('device_token', $request->device_token)
                    ->delete();
            }

            auth('api')->logout();

            return response()->json([
                'message' => __("response_message.logout"),
                'status' => 200
            ], 200);
        }

        return response()->json([
            'error' => __("response_message.signin.not_signin"),
            'status' => 401
        ], 401);
    }

    public function refreshFcmToken(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate incoming parameters
        $request->validate([
            'device_type'  => 'required|string',
            'browser_name' => 'nullable|string',
            'os_name'      => 'nullable|string',
            'fcm_token'    => 'required|string',
        ]);

        // Update or create the user's device record with the FCM token and device info
        $device = UserDevice::updateOrCreate(
            [
                'user_id'      => $user->id,
                'device_type'  => $request->device_type,
                'browser_name' => $request->browser_name,
            ],
            [
                'device_token' => $request->fcm_token,
                'device_type'  => $request->device_type,
                'browser_name' => $request->browser_name,
                'os_name'      => $request->os_name,
                'is_active'    => true,
            ]
        );

        return response()->json([
            'message' => 'FCM token updated successfully',
            'device'  => $device,
        ]);
    }

    public function emp_login(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status' => 401,
                'error' => 'Invalid email or password'
            ], 401);
        }
        $user = auth('api')->user();
        
        // Apply IP restriction only for internal users
        $ipAddress = $request->ip();

        $isAllowed = DB::table('IPWhitelist')
            ->where('strIPAddress', $ipAddress)
            ->where('isActive', 1)
            ->exists();

        if (!$isAllowed) {
            auth('api')->logout();
            return response()->json([
                'status' => 403,
                'error' => 'Access denied. Your IP address is not whitelisted.'
            ], 403);
        }

        $allowedRoles = [
            'DataEntryOperator',
            'SuperAdmin',
            'Manager',
            'Supervisor'
        ];

        if (!in_array($user->type, $allowedRoles)) {

            auth('api')->logout();

            return response()->json([
                'status' => 403,
                'error' => 'Client users are not allowed to login.'
            ], 403);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Login successful',
            'user' => auth('api')->user(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ], 200);
    }
}