<?php

namespace RonasIT\Chat\Notifications\Resources;

use RonasIT\Chat\Contracts\Notifications\Resources\MessageNotificationResourceContract;

class MessagesCollectionResource extends NotificationResourceCollection
{
    public string $collects = MessageNotificationResourceContract::class;
}
