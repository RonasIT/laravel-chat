<?php

namespace RonasIT\Chat\Notifications;

use RonasIT\Chat\Models\Conversation;

abstract class BaseConversationNotification extends BaseNotification
{
    public function __construct(
        protected readonly Conversation $conversation,
        int $recipientId,
    ) {
        parent::__construct($recipientId);
    }
}
