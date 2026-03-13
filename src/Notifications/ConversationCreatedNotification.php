<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Contracts\Notifications\ConversationCreatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\Resources\ConversationNotificationResourceContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;

class ConversationCreatedNotification extends BaseConversationNotification implements ConversationCreatedNotificationContract
{
    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage([
            'conversation' => app(ConversationNotificationResourceContract::class, [
                'resource' => $this->conversation->load('last_message'),
            ]),
        ]);
    }

    public function broadcastType(): string
    {
        return BroadcastNotificationTypeEnum::ConversationCreated->value;
    }
}
