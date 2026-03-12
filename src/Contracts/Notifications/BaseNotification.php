<?php

namespace RonasIT\Chat\Contracts\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification
{
    abstract public function via($notifiable): array;

    abstract public function toBroadcast(): BroadcastMessage;

    abstract public function broadcastType(): string;
}
