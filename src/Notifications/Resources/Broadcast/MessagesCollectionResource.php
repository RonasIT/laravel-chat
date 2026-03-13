<?php

namespace RonasIT\Chat\Notifications\Resources\Broadcast;

class MessagesCollectionResource extends BroadcastResourceCollection
{
    public string $collects = MessageResource::class;
}
