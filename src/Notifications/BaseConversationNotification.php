<?php

namespace RonasIT\Chat\Notifications;

use RonasIT\Chat\Contracts\Notifications\ConversationNotificationContract;
use RonasIT\Chat\Models\Conversation;

abstract class BaseConversationNotification extends BaseNotification implements ConversationNotificationContract
{
    protected Conversation $conversation;

    public function setConversation(Conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }
}
