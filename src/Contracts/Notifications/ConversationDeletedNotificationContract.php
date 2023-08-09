<?php

namespace RonasIT\Chat\Contracts\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Database\Eloquent\Model;

interface ConversationDeletedNotificationContract
{
    function via($notifiable): array;

    function toBroadcast(): BroadcastMessage;

    function setConversation(Model $conversation): self;
}
