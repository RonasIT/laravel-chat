<?php

namespace RonasIT\Chat\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use RonasIT\Chat\Enums\Conversation\TypeEnum;
use RonasIT\Support\Traits\ModelTrait;

class Conversation extends Model
{
    use ModelTrait;

    protected $fillable = [
        'creator_id',
        'type',
        'title',
        'cover_id',
        'last_updated_at',
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'type' => TypeEnum::class,
    ];

    public function last_message(): HasOne
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('chat.classes.user_model'));
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            related: config('chat.classes.user_model'),
            table: 'conversation_member',
            foreignPivotKey: 'conversation_id',
            relatedPivotKey: 'member_id',
        );
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(config('chat.classes.media_model'), 'cover_id');
    }

    public function scopeWithUnreadMessagesCount(Builder $query, int $memberId): Builder
    {
        return $query->withCount([
            'messages as unread_messages_count' => fn ($query) => $query
                ->whereNot('sender_id', $memberId)
                ->whereDoesntHave('reads', fn ($query) => $query->where('read_messages.member_id', $memberId)),
        ]);
    }

    public function pinned_messages(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                related: Message::class,
                table: 'pinned_messages',
                foreignPivotKey: 'conversation_id',
                relatedPivotKey: 'message_id',
            )
            ->orderByPivot('id', 'desc')
            ->withTimestamps();
    }

    public function hasMember(Model $member): bool
    {
        return $this->members()->where('member_id', $member->id)->exists();
    }

    public function isCreator(Model $member): bool
    {
        return $this->getAttribute('creator_id') === $member->id;
    }

    public function isGroup(): bool
    {
        return $this->getAttribute('type') === TypeEnum::Group;
    }

    public function isPrivate(): bool
    {
        return $this->getAttribute('type') === TypeEnum::Private;
    }
}
