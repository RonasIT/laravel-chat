<?php

namespace RonasIT\Chat\Contracts\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;

interface NotificationContract
{
    public function setRecipientId(int $recipientId): self;

    public function via($notifiable): array;

    public function broadcastType(): string;

    public function broadcastOn(): array;

    public function toBroadcast(): BroadcastMessage;
}
