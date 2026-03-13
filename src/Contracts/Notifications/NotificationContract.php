<?php

namespace RonasIT\Chat\Contracts\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

abstract class NotificationContract extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    abstract public function via($notifiable): array;

    abstract public function toBroadcast(): BroadcastMessage;

    abstract public function broadcastType(): string;
}
