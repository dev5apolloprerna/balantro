<?php
// app/Services/DeviceTokenService.php

namespace App\Services;

use App\Models\User;
use App\Models\UserDevice;

class DeviceTokenService
{
    /**
     * Store or update device token for user
     */
    public function storeOrUpdateToken($userId, $deviceToken, $deviceType = 'mobile', $deviceInfo = [])
    {
        try {
            // Check if token already exists for this user
            $existingDevice = UserDevice::where('user_id', $userId)
                ->where('device_token', $deviceToken)
                ->first();

            if ($existingDevice) {
                // Update existing device
                $existingDevice->update([
                    'device_type' => $deviceType,
                    'device_name' => $deviceInfo['device_name'] ?? null,
                    'os' => $deviceInfo['os'] ?? null,
                    'browser' => $deviceInfo['browser'] ?? null,
                    'is_active' => true,
                ]);

                return [
                    'success' => true,
                    'action' => 'updated',
                    'device' => $existingDevice
                ];
            }

            // Check if user has too many devices (optional: limit devices per user)
            $this->checkDeviceLimit($userId);

            // Create new device token
            $device = UserDevice::create([
                'user_id' => $userId,
                'device_token' => $deviceToken,
                'device_type' => $deviceType,
                'device_name' => $deviceInfo['device_name'] ?? null,
                'os' => $deviceInfo['os'] ?? null,
                'browser' => $deviceInfo['browser'] ?? null,
                'is_active' => true,
            ]);

            return [
                'success' => true,
                'action' => 'created',
                'device' => $device
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Remove device token (on logout or app uninstall)
     */
    public function removeToken($userId, $deviceToken)
    {
        try {
            $deleted = UserDevice::where('user_id', $userId)
                ->where('device_token', $deviceToken)
                ->delete();

            return [
                'success' => true,
                'deleted' => $deleted
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Deactivate token (soft delete alternative)
     */
    public function deactivateToken($userId, $deviceToken)
    {
        try {
            $updated = UserDevice::where('user_id', $userId)
                ->where('device_token', $deviceToken)
                ->update(['is_active' => false]);

            return [
                'success' => true,
                'updated' => $updated
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all active tokens for user
     */
    public function getUserTokens($userId, $deviceType = null)
    {
        $query = UserDevice::where('user_id', $userId)->active();

        if ($deviceType) {
            $query->where('device_type', $deviceType);
        }

        return $query->pluck('device_token')->toArray();
    }

    /**
     * Limit devices per user (optional)
     */
    private function checkDeviceLimit($userId, $maxDevices = 5)
    {
        $deviceCount = UserDevice::where('user_id', $userId)->count();

        if ($deviceCount >= $maxDevices) {
            // Remove oldest device
            UserDevice::where('user_id', $userId)
                ->orderBy('created_at', 'asc')
                ->limit(1)
                ->delete();
        }
    }

    /**
     * Detect device type from user agent
     */
    public function detectDeviceType($userAgent)
    {
        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'mobile') !== false) {
            return 'mobile';
        }

        if (strpos($userAgent, 'tablet') !== false) {
            return 'tablet';
        }

        return 'pc';
    }

    /**
     * Get device info from user agent
     */
    public function getDeviceInfo($userAgent)
    {
        return [
            'os' => $this->detectOS($userAgent),
            'browser' => $this->detectBrowser($userAgent),
            'device_name' => $this->detectDeviceName($userAgent),
        ];
    }

    private function detectOS($userAgent)
    {
        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'windows') !== false) return 'Windows';
        if (strpos($userAgent, 'mac') !== false) return 'Mac';
        if (strpos($userAgent, 'linux') !== false) return 'Linux';
        if (strpos($userAgent, 'android') !== false) return 'Android';
        if (strpos($userAgent, 'ios') !== false || strpos($userAgent, 'iphone') !== false) return 'iOS';

        return 'Unknown';
    }

    private function detectBrowser($userAgent)
    {
        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'safari') !== false) return 'Safari';
        if (strpos($userAgent, 'edge') !== false) return 'Edge';
        if (strpos($userAgent, 'opera') !== false) return 'Opera';

        return 'Unknown';
    }

    private function detectDeviceName($userAgent)
    {
        // Simple detection - you can enhance this
        $deviceType = $this->detectDeviceType($userAgent);
        return ucfirst($deviceType) . ' Device';
    }
}
