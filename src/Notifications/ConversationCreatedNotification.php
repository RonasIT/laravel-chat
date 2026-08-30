<?php

namespace RonasIT\Chat\Notifications;

use RonasIT\Chat\Contracts\Notifications\ConversationCreatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\Resources\Broadcast\ConversationResourceContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;

class ConversationCreatedNotification extends BaseConversationNotification implements ConversationCreatedNotificationContract
{
    public function getBroadcastData(): array
    {
        $conversation = app(ConversationServiceContract::class)
            ->with('last_message.sender')
            ->find($this->conversationId);

        return [
            'data' => app(ConversationResourceContract::class, [
                'resource' => $conversation,
            ]),
        ];
    }

    public function broadcastAs(): string
    {
        return BroadcastNotificationTypeEnum::ConversationCreated->value;
    }
}
