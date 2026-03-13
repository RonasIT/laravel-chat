<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Contracts\Notifications\MessageUpdatedNotificationContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;
use RonasIT\Chat\Notifications\Resources\MessageResource;

class MessageUpdatedNotification extends BaseMessageNotification implements MessageUpdatedNotificationContract
{
    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => new MessageResource($this->message),
        ]);
    }

    public function broadcastType(): string
    {
        return BroadcastNotificationTypeEnum::MessageUpdated->value;
    }
}
