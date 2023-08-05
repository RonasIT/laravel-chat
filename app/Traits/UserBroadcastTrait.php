<?php

namespace App\Traits;

trait UserBroadcastTrait
{
    public function receivesBroadcastNotificationsOn(): string
    {
        return "users.{$this->id}";
    }
}
