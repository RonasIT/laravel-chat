<?php

namespace RonasIT\Chat\Contracts\Notifications;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;

interface NewMessageNotificationContract
{
    function via($notifiable): array;

    function toBroadcast(): BroadcastMessage;

    function setMessage(Model $message): self;
}
