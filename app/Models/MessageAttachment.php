<?php
// app/Models/MessageAttachment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    protected $fillable = ['message_id', 'original_name', 'file_name', 'mime', 'size', 'url'];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
