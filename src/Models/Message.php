<?php

namespace RonasIT\Chat\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use RonasIT\Support\Traits\ModelTrait;

class Message extends Model
{
    use ModelTrait;

    protected $fillable = [
        'conversation_id',
        'sender_id',
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

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(config('chat.classes.media_model'), 'attachment_id');
    }

    public function scopeWithIsRead(Builder $query): Builder
    {
        return $query->withExists([
            'members_who_read_message as is_read' => fn ($query) => $query
                ->whereColumn('read_messages.member_id', '!=', 'messages.sender_id'),
        ]);
    }

    public function members_who_read_message(): BelongsToMany
    {
        return $this->belongsToMany(
            related: config('chat.classes.user_model'),
            table: 'read_messages',
            foreignPivotKey: 'message_id',
            relatedPivotKey: 'member_id',
        );
    }
}
