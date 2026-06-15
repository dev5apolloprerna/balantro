<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;


class Conversation extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'org_id',
        'client_id',
        'agent_id',
        'subject',
        'status',
        'last_message_at',
        'last_message_id',
        'unread_for_client',
        'unread_for_agent'
    ];

    // Relations
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
    public function lastMessage()
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    // Scopes used in controllers
    public function scopeForClient(Builder $q, int $clientId): Builder
    {
        return $q->where('client_id', $clientId);
    }

    public function scopeForOperator(Builder $q, int $agentId): Builder
    {
        return $q->where('agent_id', $agentId);
    }

    // Inline last-message meta (handy for lists)
    public function scopeWithLastMessageMeta(Builder $q): Builder
    {
        return $q
            ->addSelect([
                'last_message' => Message::select('body')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest('id')->limit(1),
                'last_message_at' => Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest('id')->limit(1),
            ]);
    }
}
