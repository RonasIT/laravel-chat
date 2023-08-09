<?php

namespace RonasIT\Chat\Traits;

trait UserBroadcastTrait
{
    public function receivesBroadcastNotificationsOn(): string
    {
        return "users.{$this->id}";
    }
}
