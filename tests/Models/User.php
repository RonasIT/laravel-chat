<?php

namespace RonasIT\Chat\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use RonasIT\Chat\Models\Message;
use RonasIT\Support\Traits\ModelTrait;

class User extends Authenticatable
{
    use HasFactory;
    use ModelTrait;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
    ];

    protected $hidden = ['pivot'];

    public function sender_messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
}
