<?php

namespace RonasIT\Chat\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
            'read_receipts as is_read' => fn ($query) => $query
                ->whereColumn('read_messages.member_id', '!=', 'messages.sender_id'),
        ]);
    }

    public function read_receipts(): HasMany
    {
        return $this->hasMany(ReadMessage::class, 'message_id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('with_is_read', fn (Builder $query) => $query->withIsRead());
    }
}
