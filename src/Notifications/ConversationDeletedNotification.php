<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use RonasIT\Chat\Contracts\Notifications\ConversationDeletedNotificationContract;

class ConversationDeletedNotification extends Notification implements ConversationDeletedNotificationContract, ShouldBroadcast, ShouldQueue
{
    use Queueable;

    protected array $conversation;
    protected Model $notifiable;

    public function setConversation(array $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function setNotifiable(Model $notifiable): self
    {
        $this->notifiable = $notifiable;

        return $this;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel("conversation.{$this->notifiable->id}")];
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
