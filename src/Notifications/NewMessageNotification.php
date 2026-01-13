<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use RonasIT\Chat\Contracts\Notifications\NewMessageNotificationContract;

class NewMessageNotification extends Notification implements NewMessageNotificationContract, ShouldBroadcast, ShouldQueue
{
    use Queueable;

    protected Model $sender;
    protected Model $message;

    public function __construct()
    {
        $this->sender = Auth::user();
    }

    public function setMessage(Model $message): self
    {
        $this->message = $message;

        return $this;
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
}
