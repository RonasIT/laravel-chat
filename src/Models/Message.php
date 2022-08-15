<?php

namespace RonasIT\Chat\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'recipient_id',
        'is_read',
        'text',
        'attachment_id',
    ];

    protected $hidden = ['pivot'];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(config('chat.classes.user_model'));
    }

    public function recipient()
    {
        return $this->belongsTo(config('chat.classes.user_model'));
    }

    public function attachment()
    {
        return $this->belongsTo(config('chat.classes.user_model'), 'attachment_id');
    }
}
