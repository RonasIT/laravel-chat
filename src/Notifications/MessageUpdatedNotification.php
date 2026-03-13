<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;
use RonasIT\Chat\Contracts\Notifications\MessageUpdatedNotificationContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;
use RonasIT\Chat\Models\Message;
use RonasIT\Chat\Notifications\Resources\MessageResource;

class MessageUpdatedNotification extends MessageUpdatedNotificationContract
{
    public function __construct(
        protected readonly Message $message,
        protected readonly int $recipientId,
    ) {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel("chat.{$this->recipientId}")];
    }

    public function via($notifiable): array
    {
        return config('chat.default_channels');
    }

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
