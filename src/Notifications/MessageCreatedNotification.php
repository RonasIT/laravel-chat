<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Contracts\Notifications\Resources\Broadcast\MessageResourceContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;

class MessageCreatedNotification extends BaseMessageNotification
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
        return BroadcastNotificationTypeEnum::MessageCreated->value;
    }
}
