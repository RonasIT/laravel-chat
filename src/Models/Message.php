<?php

namespace RonasIT\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use RonasIT\Chat\Contracts\Models\UserContract;
use RonasIT\Chat\Contracts\Models\UserMediaContract;

class Message extends Model
{
    const TYPE_USUAL = 'usual';
    const TYPE_ORDER = 'order';

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'recipient_id',
        'is_read',
        'text',
        'attachment_id',
        'type',
        'order',
    ];

    protected $hidden = ['pivot'];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(UserContract::class);
    }

    public function recipient()
    {
        return $this->belongsTo(UserContract::class);
    }

    public function attachment()
    {
        return $this->belongsTo(UserMediaContract::class, 'attachment_id');
    }
}
