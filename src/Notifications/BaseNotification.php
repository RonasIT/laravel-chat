<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use RonasIT\Chat\Contracts\Notifications\NotificationContract;

abstract class BaseNotification extends Notification implements NotificationContract
{
    use Queueable;

    protected readonly int $recipientId;

    public function setRecipientId(int $recipientId): self
    {
        $this->recipientId = $recipientId;

        return $this;
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
