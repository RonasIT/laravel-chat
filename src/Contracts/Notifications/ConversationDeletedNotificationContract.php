<?php

namespace RonasIT\Chat\Contracts\Notifications;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;

interface ConversationDeletedNotificationContract
{
    public function via($notifiable): array;

    public function toBroadcast(): BroadcastMessage;

    public function setConversation(array $conversation): self;

    public function setNotifiable(Model $notifiable): self;
}
