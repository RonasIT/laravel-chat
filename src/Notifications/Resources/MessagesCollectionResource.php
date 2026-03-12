<?php

namespace RonasIT\Chat\Notifications\Resources;

class MessagesCollectionResource extends NotificationResourceCollection
{
    public string $collects = MessageResource::class;
}
