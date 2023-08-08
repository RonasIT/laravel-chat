<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Channels\BroadcastChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Contracts\Notifications\ConversationDeletedNotificationContract;
use Illuminate\Database\Eloquent\Model;

class ConversationDeletedNotification extends Notification implements ShouldBroadcast, ShouldQueue, ConversationDeletedNotificationContract
{
    use Queueable;

    protected Model $conversation;

    public function setConversation(Model $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function via($notifiable): array
    {
        return [BroadcastChannel::class];
    }

    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage(['conversation' => $this->conversation]);
    }
}