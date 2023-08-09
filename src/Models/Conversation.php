<?php

namespace RonasIT\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use RonasIT\Support\Traits\ModelTrait;

class Conversation extends Model
{
    use ModelTrait;

    protected $fillable = [
        'sender_id',
        'recipient_id',
    ];

    protected $hidden = ['pivot'];

    public function last_message(): HasOne
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(config('chat.classes.user_model'));
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(config('chat.classes.user_model'));
    }
}
