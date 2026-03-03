<?php

namespace RonasIT\Chat\Models;

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

    public function hasMember(Model $member): bool
    {
        return $this->members()->where('member_id', $member->id)->exists();
    }

    public function isCreator(Model $member): bool
    {
        return $this->getAttribute('creator_id') === $member->id;
    }
}
