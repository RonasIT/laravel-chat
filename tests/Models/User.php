<?php

namespace RonasIT\Chat\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Models\Message;
use RonasIT\Media\Models\Media;
use RonasIT\Support\Traits\ModelTrait;

class User extends Authenticatable
{
    use Notifiable;
    use ModelTrait;
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
    ];

    public function sendedConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'sender_id');
    }

    public function recipientedConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'recipient_id');
    }

    public function sendedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function recipientedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }
}
