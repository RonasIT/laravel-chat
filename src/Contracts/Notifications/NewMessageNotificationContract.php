<?php

namespace RonasIT\Chat\Contracts\Notifications;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;

interface NewMessageNotificationContract
{
    function via($notifiable): array;

    function toBroadcast(): BroadcastMessage;

    function toExpoPush(): ExpoMessage;

    function setMessage(Model $message);
}
