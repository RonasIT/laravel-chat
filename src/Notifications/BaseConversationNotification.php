<?php

namespace RonasIT\Chat\Notifications;

abstract class BaseConversationNotification extends BaseNotification
{
    public function __construct(
        protected readonly int $conversationId,
        int $recipientId,
    ) {
        parent::__construct($recipientId);
    }
}
