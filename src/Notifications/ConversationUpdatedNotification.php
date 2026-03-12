<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Contracts\Notifications\ConversationUpdatedNotificationContractContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Notifications\Resources\ConversationResource;

class ConversationUpdatedNotification extends ConversationUpdatedNotificationContractContract
{
    public function __construct(
        protected readonly Conversation $conversation,
        protected readonly int $recipientId,
    ) {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel("chat.{$this->recipientId}")];
    }

    public function via($notifiable): array
    {
        return config('chat.default_channels');
    }

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
            'conversation' => new ConversationResource($conversation),
        ]);
    }

    public function broadcastType(): string
    {
        return BroadcastNotificationTypeEnum::ConversationUpdated->value;
    }
}
