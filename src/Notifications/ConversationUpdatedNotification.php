<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Contracts\Notifications\ConversationUpdatedNotificationContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Contracts\Notifications\Resources\ConversationNotificationResourceContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;

class ConversationUpdatedNotification extends BaseConversationNotification implements ConversationUpdatedNotificationContract
{
    public function toBroadcast(): BroadcastMessage
    {
        $conversation = app(ConversationServiceContract::class)
            ->with([
                'last_message',
                'pinned_messages',
            ])
            ->withCount('members')
            ->withUnreadCountMemberId($this->recipientId)
            ->find($this->conversation->id);

        return new BroadcastMessage([
            'conversation' => app(ConversationNotificationResourceContract::class, ['resource' => $conversation]),
        ]);
    }

    public function broadcastType(): string
    {
        return BroadcastNotificationTypeEnum::ConversationUpdated->value;
    }
}
