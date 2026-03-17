<?php

namespace RonasIT\Chat\Notifications\Resources\Broadcast;

use RonasIT\Chat\Contracts\Resources\MessageResourceContract;

class MessagesCollectionResource extends BroadcastResourceCollection
{
    public string $collects = MessageResourceContract::class;
}
