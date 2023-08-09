<?php

namespace RonasIT\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RonasIT\Support\Traits\ModelTrait;

class Message extends Model
{
    use ModelTrait;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'recipient_id',
        'is_read',
        'text',
        'attachment_id',
    ];

    protected $hidden = ['pivot'];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(config('chat.classes.user_model'));
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(config('chat.classes.user_model'));
    }

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(config('chat.classes.media_model'), 'attachment_id');
    }
}
