<?php

namespace RonasIT\Chat\Notifications;

use RonasIT\Chat\Contracts\Notifications\MessageCreatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\Resources\Broadcast\MessageResourceContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;

class MessageCreatedNotification extends BaseMessageNotification implements MessageCreatedNotificationContract
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
        return BroadcastNotificationTypeEnum::MessageCreated->value;
    }
}
