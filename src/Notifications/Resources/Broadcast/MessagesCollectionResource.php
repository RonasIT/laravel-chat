<?php

namespace RonasIT\Chat\Notifications\Resources\Broadcast;

use RonasIT\Chat\Contracts\Notifications\Resources\Broadcast\MessageResourceContract;

class MessagesCollectionResource extends BroadcastResourceCollection
{
    public string $collects = MessageResourceContract::class;
}
