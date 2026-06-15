<?php

namespace App\Http\Controllers;

use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(FirebaseNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Send notification to single device
     */
    public function sendSingleNotification(Request $request)
    {
        $request->validate([
            'device_token' => 'required',
            'title' => 'required',
            'body' => 'required',
        ]);

        $result = $this->notificationService->sendToDevice(
            $request->device_token,
            $request->title,
            $request->body,
            $request->data ?? [] // optional custom data
        );

        if ($result['success']) {
            return response()->json(['message' => 'Notification sent successfully']);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Send notification to multiple devices
     */
    public function sendBulkNotification(Request $request)
    {
        $request->validate([
            'device_tokens' => 'required|array',
            'title' => 'required',
            'body' => 'required',
        ]);

        $result = $this->notificationService->sendToMultipleDevices(
            $request->device_tokens,
            $request->title,
            $request->body,
            $request->data ?? []
        );

        return response()->json($result);
    }

    /**
     * Send notification to topic
     */
    public function sendTopicNotification(Request $request)
    {
        $request->validate([
            'topic' => 'required',
            'title' => 'required',
            'body' => 'required',
        ]);

        $result = $this->notificationService->sendToTopic(
            $request->topic,
            $request->title,
            $request->body,
            $request->data ?? []
        );

        return response()->json($result);
    }

    /**
     * Subscribe device to topic
     */
    public function subscribeToTopic(Request $request)
    {
        $request->validate([
            'device_token' => 'required',
            'topic' => 'required',
        ]);

        $result = $this->notificationService->subscribeToTopic(
            $request->device_token,
            $request->topic
        );

        return response()->json($result);
    }
}
