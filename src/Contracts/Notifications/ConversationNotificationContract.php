<?php

namespace RonasIT\Chat\Contracts\Notifications;

use RonasIT\Chat\Models\Conversation;

interface ConversationNotificationContract extends NotificationContract
{
    public function setConversation(Conversation $conversation): self;
}
