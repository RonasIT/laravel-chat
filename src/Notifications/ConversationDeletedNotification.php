<?php

namespace RonasIT\Chat\Notifications;

use RonasIT\Chat\Contracts\Notifications\ConversationDeletedNotificationContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;

class ConversationDeletedNotification extends BaseConversationNotification implements ConversationDeletedNotificationContract
{
    public function getBroadcastData(): array
    {
        return [
            'data' => [
                'id' => $this->conversationId,
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return BroadcastNotificationTypeEnum::ConversationDeleted->value;
    }
}
