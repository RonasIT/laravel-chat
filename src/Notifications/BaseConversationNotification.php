<?php

namespace RonasIT\Chat\Notifications;

use RonasIT\Chat\Models\Conversation;

abstract class BaseConversationNotification extends BaseNotification
{
    public function __construct(
        protected Conversation $conversation,
        protected int $recipientId,
    ) {
        parent::__construct($recipientId);
    }
}
