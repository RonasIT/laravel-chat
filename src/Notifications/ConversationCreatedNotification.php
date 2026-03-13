<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;
use RonasIT\Chat\Notifications\Resources\ConversationResource;

class ConversationCreatedNotification extends BaseConversationNotification
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
