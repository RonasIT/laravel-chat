<?php

namespace RonasIT\Chat\Contracts\Notifications;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;

interface NewMessageNotificationContract
{
    public function via($notifiable): array;

    public function toBroadcast(): BroadcastMessage;

    public function setMessage(Model $message): self;
}
