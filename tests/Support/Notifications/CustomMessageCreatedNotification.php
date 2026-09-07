<?php

namespace RonasIT\Chat\Tests\Support\Notifications;

use RonasIT\Chat\Notifications\MessageCreatedNotification;

class CustomMessageCreatedNotification extends MessageCreatedNotification
{
    const string QUEUE_NAME = 'chat-heavy';

    protected function getQueueName(): ?string
    {
        return self::QUEUE_NAME;
    }
}
