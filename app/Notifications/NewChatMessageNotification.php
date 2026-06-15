<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewChatMessageNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        // Database + Broadcast (for real-time)
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'sender_id'   => $this->data['sender_id'],
            'sender_name' => $this->data['sender_name'],
            'message'     => $this->data['message'],
            'message_id'  => $this->data['message_id'],
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
