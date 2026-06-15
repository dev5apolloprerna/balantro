<?php

namespace App\Models;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'description'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function scopeMessages($query, $userId)
    {
        return $query->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId);
    }

    public function broadcastToClient($clientId, $userId, $clientHtml = null, $managementTeamHtml = null)
    {
        event(new \App\Events\ChatMessageSent([
            'sender_id' => $userId,
            'message' => $this->description,
            'receiver_message_box_id' => $clientId,
            'client_html' => $clientHtml,
            'management_team_html' => $managementTeamHtml,
            'turbo_stream' => $managementTeamHtml
        ]));
    }

    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class);
    }
}
