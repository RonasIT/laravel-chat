<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Contracts\Notifications\MessageUpdatedNotificationContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;
use RonasIT\Chat\Contracts\Notifications\Resources\MessageNotificationResourceContract;

class MessageUpdatedNotification extends BaseMessageNotification implements MessageUpdatedNotificationContract
{
    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => app(MessageNotificationResourceContract::class, ['resource' => $this->message]),
        ]);
    }

    public function broadcastType(): string
    {
        return BroadcastNotificationTypeEnum::MessageUpdated->value;
    }
}
