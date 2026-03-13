<?php

namespace RonasIT\Chat\Contracts\Notifications;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

interface NotificationContract extends ShouldBroadcast, ShouldQueue
{
    public function setRecipientId(int $recipientId): self;

    public function via($notifiable): array;

    public function broadcastType(): string;

    public function broadcastOn(): array;

    public function toBroadcast(): BroadcastMessage;
}
