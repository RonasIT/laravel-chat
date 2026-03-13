<?php

namespace RonasIT\Chat\Contracts\Notifications;

use RonasIT\Chat\Models\Message;

interface MessageNotificationContract extends NotificationContract
{
    public function setMessage(Message $message): self;
}
