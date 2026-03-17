<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use RonasIT\Chat\Contracts\Notifications\NewMessageNotificationContract;
use RonasIT\Chat\Enums\BroadcastNotificationTypeEnum;

class MessageCreatedNotification extends Notification implements NewMessageNotificationContract, ShouldBroadcast, ShouldQueue
{
    use Queueable;

    protected Model $sender;
    protected Model $message;
    protected int $recipientId;

    public function __construct()
    {
        $this->sender = Auth::user();
    }

    public function setMessage(Model $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function setRecipientId(int $id): self
    {
        $this->recipientId = $id;

        return $this;
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
            'message' => $this->message,
            'sender_first_name' => $this->sender->first_name,
            'sender_last_name' => $this->sender->last_name,
        ]);
    }

    public function broadcastAs(): string
    {
        return BroadcastNotificationTypeEnum::MessageCreated->value;
    }
}
