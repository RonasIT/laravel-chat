<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Contracts\Notifications\ConversationCreatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\Resources\Broadcast\ConversationResourceContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;

class ConversationCreatedNotification extends BaseConversationNotification implements ConversationCreatedNotificationContract
{
    public function toBroadcast(): BroadcastMessage
    {
        $conversation = app(ConversationServiceContract::class)
            ->with('last_message.sender')
            ->find($this->conversationId);

        return new BroadcastMessage([
            'data' => app(ConversationResourceContract::class, [
                'resource' => $conversation,
            ]),
        ]);
    }

    public function broadcastAs(): string
    {
        return BroadcastNotificationTypeEnum::ConversationCreated->value;
    }
}
