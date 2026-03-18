<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Contracts\Notifications\MessageUpdatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\Resources\Broadcast\MessageResourceContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;

class MessageUpdatedNotification extends BaseMessageNotification implements MessageUpdatedNotificationContract
{
    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage([
            'data' => app(MessageResourceContract::class, [
                'resource' => $this->message->load('sender'),
            ]),
        ]);
    }

    public function broadcastAs(): string
    {
        return BroadcastNotificationTypeEnum::MessageUpdated->value;
    }
}
