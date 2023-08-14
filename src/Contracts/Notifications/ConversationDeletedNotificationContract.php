<?php

namespace RonasIT\Chat\Contracts\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;

interface ConversationDeletedNotificationContract
{
    function via($notifiable): array;

    function toBroadcast(): BroadcastMessage;

    function setConversation(array $conversation): self;
}
