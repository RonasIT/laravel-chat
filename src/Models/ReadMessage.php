<?php

namespace RonasIT\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use RonasIT\Support\Traits\ModelTrait;

class ReadMessage extends Model
{
    use ModelTrait;

    protected $fillable = [
        'message_id',
        'member_id',
    ];
}
