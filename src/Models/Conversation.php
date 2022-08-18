<?php

namespace RonasIT\Chat\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
    ];

    protected $hidden = ['pivot'];

    public function last_message()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function sender()
    {
        return $this->belongsTo(config('chat.classes.user_model'));
    }

    public function recipient()
    {
        return $this->belongsTo(config('chat.classes.user_model'));
    }
}
