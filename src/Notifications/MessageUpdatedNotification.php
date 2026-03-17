<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Contracts\Notifications\MessageUpdatedNotificationContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;
use RonasIT\Chat\Notifications\Resources\Broadcast\MessageResource;

class MessageUpdatedNotification extends BaseMessageNotification implements MessageUpdatedNotificationContract
{
    public function toBroadcast(): BroadcastMessage
    {
        $this->message->load('sender');

        return new BroadcastMessage([
            'data' => new MessageResource($this->message),
        ]);
    }

    public function broadcastAs(): string
    {
        return BroadcastNotificationTypeEnum::MessageUpdated->value;
    }
}
