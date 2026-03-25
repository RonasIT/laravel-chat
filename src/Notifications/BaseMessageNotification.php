<?php

namespace RonasIT\Chat\Notifications;

abstract class BaseMessageNotification extends BaseNotification
{
    public function __construct(
        protected readonly int $messageId,
        int $recipientId,
    ) {
        parent::__construct($recipientId);
    }
}
