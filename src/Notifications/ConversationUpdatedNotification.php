<?php

namespace RonasIT\Chat\Notifications;

use RonasIT\Chat\Contracts\Notifications\ConversationUpdatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\Resources\Broadcast\ConversationResourceContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;

class ConversationUpdatedNotification extends BaseConversationNotification implements ConversationUpdatedNotificationContract
{
    public function getBroadcastData(): array
    {
        $conversation = app(ConversationServiceContract::class)
            ->with([
                'last_message.sender',
                'pinned_messages',
            ])
            ->withCount('members')
            ->withUnreadCountMemberId($this->recipientId)
            ->find($this->conversationId);

        return [
            'data' => app(ConversationResourceContract::class, [
                'resource' => $conversation,
            ]),
        ];
    }

    public function broadcastAs(): string
    {
        return BroadcastNotificationTypeEnum::ConversationUpdated->value;
    }
}
