<?php

namespace RonasIT\Chat\Notifications;

use RonasIT\Chat\Models\Message;

abstract class BaseMessageNotification extends BaseNotification
{
    public function __construct(
        protected readonly Message $message,
        int $recipientId,
    ) {
        parent::__construct($recipientId);
    }
}
