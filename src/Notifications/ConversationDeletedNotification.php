<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Contracts\Notifications\ConversationDeletedNotificationContract;

class ConversationDeletedNotification extends Notification implements ShouldBroadcast, ShouldQueue, ConversationDeletedNotificationContract
{
    use Queueable;

    protected array $conversation;

    public function setConversation(array $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function via($notifiable): array
    {
        return config('chat.default_channels');
    }

    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage(['conversation' => $this->conversation]);
    }
}