<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Contracts\Notifications\ConversationCreatedNotificationContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;
use RonasIT\Chat\Notifications\Resources\ConversationResource;

class ConversationCreatedNotification extends ConversationCreatedNotificationContract
{
    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage([
            'conversation' => new ConversationResource($this->conversation->load('last_message')),
        ]);
    }

    public function broadcastType(): string
    {
        return BroadcastNotificationTypeEnum::ConversationCreated->value;
    }
}
