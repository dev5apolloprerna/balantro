<?php

namespace App\Http\Controllers;

use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDevicesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_token' => 'required',
            'device_type' => 'nullable',
            'browser_name' => 'nullable',
            'os_name' => 'nullable'
        ]);

        try {
            $device = Auth::user()->devices()->updateOrCreate(
                ['device_token' => $validated['device_token']],
                $validated
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'user_device.device_token' => 'required'
        ]);

        $token = $request->input('user_device.device_token');
        $device = Auth::user()->devices()->where('device_token', $token)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'error' => 'Device not found'
            ], 404);
        }

        try {
            $device->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}