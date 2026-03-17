<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Contracts\Notifications\ConversationDeletedNotificationContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;

class ConversationDeletedNotification extends BaseConversationNotification implements ConversationDeletedNotificationContract
{
    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage(['data' => [
            'id' => $this->conversationId,
        ]]);
    }

    public function broadcastAs(): string
    {
        return BroadcastNotificationTypeEnum::ConversationDeleted->value;
    }
}
