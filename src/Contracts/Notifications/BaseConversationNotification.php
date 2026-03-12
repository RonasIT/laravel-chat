<?php

namespace RonasIT\Chat\Contracts\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use RonasIT\Chat\Models\Conversation;

abstract class BaseConversationNotification extends BaseNotification
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
}
