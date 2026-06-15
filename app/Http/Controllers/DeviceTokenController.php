<?php
// app/Http/Controllers/DeviceTokenController.php

namespace App\Http\Controllers;

use App\Services\DeviceTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceTokenController extends Controller
{
    protected $deviceTokenService;

    public function __construct(DeviceTokenService $deviceTokenService)
    {
        $this->deviceTokenService = $deviceTokenService;
    }

    /**
     * Store device token (for login/registration)
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'device_type' => 'sometimes|in:mobile,pc,tablet',
        ]);

        $user = Auth::user();

        // Auto-detect device type if not provided
        $deviceType = $request->device_type ?? $this->deviceTokenService->detectDeviceType($request->userAgent());

        // Get device info
        $deviceInfo = $this->deviceTokenService->getDeviceInfo($request->userAgent());

        $result = $this->deviceTokenService->storeOrUpdateToken(
            $user->id,
            $request->device_token,
            $deviceType,
            $deviceInfo
        );

        if ($result['success']) {
            return response()->json([
                'message' => 'Device token ' . $result['action'] . ' successfully',
                'device' => $result['device']
            ]);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Remove device token (for logout)
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string'
        ]);

        $user = Auth::user();
        $result = $this->deviceTokenService->removeToken($user->id, $request->device_token);

        if ($result['success']) {
            return response()->json([
                'message' => 'Device token removed successfully'
            ]);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Get user's device tokens
     */
    public function index()
    {
        $user = Auth::user();
        $devices = $user->devices()->active()->get();

        return response()->json([
            'devices' => $devices
        ]);
    }
}
