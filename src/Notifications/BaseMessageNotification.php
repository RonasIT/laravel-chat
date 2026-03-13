<?php

namespace RonasIT\Chat\Notifications;

use RonasIT\Chat\Contracts\Notifications\MessageNotificationContract;
use RonasIT\Chat\Models\Message;

abstract class BaseMessageNotification extends BaseNotification implements MessageNotificationContract
{
    protected readonly Message $message;

    public function setMessage(Message $message): self
    {
        $this->message = $message;

        return $this;
    }
}
