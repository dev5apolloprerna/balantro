<?php

namespace App\Services;

use App\Jobs\FirebasePushNotificationJob;
use App\Models\User;

class NotificationService
{
    public static function sendSuperadminOnClientCreate(User $client)
    {
        User::where('role', User::ROLE_SUPER_ADMIN)->each(function($superadmin) use ($client) {
            $title = __("clients.notification.client_register.headline");
            $body = __("clients.notification.client_register.text", ['client_name' => $client->name]);

            FirebasePushNotificationJob::dispatch(
                $superadmin->id,
                $title,
                $body,
                [
                    'client_name' => $client->name,
                    'click_action' => "/admin/clients"
                ]
            );
        });
    }

    public static function sendAssignmentNotification(User $assigner, User $assignee, User $recipient)
    {
        $title = __("clients.notification.recipient_assign.headline");
        $message = self::generateMessage($assigner, $assignee, $recipient);

        $rolePathMap = [
            User::ROLE_CLIENT => 'clients',
            User::ROLE_DATA_ENTRY_OPERATOR => 'data_entry_operators',
            User::ROLE_SUPERVISOR => 'supervisors',
            User::ROLE_MANAGER => 'managers',
            User::ROLE_SUPER_ADMIN => 'super_admins'
        ];

        $assigneePath = $rolePathMap[$assignee->role] ?? 'users';
        $recipientPath = $rolePathMap[$recipient->role] ?? 'users';

        FirebasePushNotificationJob::dispatch(
            $assignee->id,
            $title,
            $message,
            [
                'recipient_name' => $recipient->name,
                'click_action' => "/$assigneePath/$recipientPath"
            ]
        );
    }

    protected static function generateMessage(User $assigner, User $assignee, User $recipient)
    {
        if ($assigner->isSuperAdmin()) {
            return __("clients.notification.recipient_assign.superadmin_text", ['recipient_name' => $recipient->name]);
        } elseif ($assigner->isManager()) {
            return __("clients.notification.recipient_assign.manager_text", [
                'recipient_name' => $recipient->name,
                'assigner_name' => $assigner->name
            ]);
        } elseif ($assigner->isSupervisor()) {
            return __("clients.notification.recipient_assign.supervisor_text", [
                'recipient_name' => $recipient->name,
                'assigner_name' => $assigner->name
            ]);
        }

        return __("clients.notification.recipient_assign.superadmin_text", ['recipient_name' => $recipient->name]);
    }
}