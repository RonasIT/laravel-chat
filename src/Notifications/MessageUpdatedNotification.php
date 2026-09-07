<?php

namespace RonasIT\Chat\Notifications;

use RonasIT\Chat\Contracts\Notifications\MessageUpdatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\Resources\Broadcast\MessageResourceContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;

class MessageUpdatedNotification extends BaseMessageNotification implements MessageUpdatedNotificationContract
{
    public function getBroadcastData(): array
    {
        $message = app(MessageServiceContract::class)
            ->with('sender')
            ->find($this->messageId);

        return [
            'data' => app(MessageResourceContract::class, [
                'resource' => $message,
            ]),
        ];
    }

    public function broadcastAs(): string
    {
        return BroadcastNotificationTypeEnum::MessageUpdated->value;
    }
}
