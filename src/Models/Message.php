<?php

namespace RonasIT\Chat\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
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

    protected $appends = ['is_read'];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function getIsReadAttribute()
    {
        return Arr::get($this->attributes, 'is_read', false);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(config('chat.classes.user.model'));
    }

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(config('chat.classes.media.model'), 'attachment_id');
    }

    public function scopeWithIsRead(Builder $query): Builder
    {
        return $query->withExists([
            'reads as is_read' => fn ($query) => $query
                ->whereColumn('read_messages.member_id', '!=', 'messages.sender_id'),
        ]);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(ReadMessage::class, 'message_id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('with_is_read', fn (Builder $query) => $query->withIsRead());
    }
}
