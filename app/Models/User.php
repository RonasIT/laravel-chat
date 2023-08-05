<?php

namespace App\Models;

use App\Traits\UserBroadcastTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use RonasIT\Support\Traits\ModelTrait;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use ModelTrait;
    use UserBroadcastTrait;

    protected $fillable = [
        'email',
        'avatar_id',
        'password',
        'role_id'
    ];

    protected $hidden = [
        'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
