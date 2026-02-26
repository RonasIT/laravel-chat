<?php

namespace RonasIT\Chat\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
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
        return $query->addSelect([
            'is_read' => DB::table('conversation_member')
                ->selectRaw('COALESCE(MAX(last_read_message_id), 0) >= messages.id')
                ->whereColumn('conversation_id', 'messages.conversation_id'),
        ]);
    }
}
