<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\WithoutRelations;
use RonasIT\Chat\Contracts\Notifications\NotificationContract;

#[WithoutRelations]
abstract class BaseNotification extends Notification implements NotificationContract
{
    use Queueable;

    public function __construct(
        protected readonly int $recipientId,
    ) {
        $this->afterCommit();
    }

    abstract public function getBroadcastData(): array;

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
        return (new BroadcastMessage($this->getBroadcastData()))
            ->onQueue($this->getQueueName());
    }

    public function viaQueues(): array
    {
        return array_fill_keys(config('chat.default_channels'), $this->getQueueName());
    }

    protected function getQueueName(): ?string
    {
        return config('chat.broadcast_queue');
    }
}
