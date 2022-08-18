<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Auth;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;
use RonasIT\Chat\Contracts\Notifications\NewMessageNotificationContract;

class NewMessageNotification extends Notification implements ShouldBroadcast, ShouldQueue, NewMessageNotificationContract
{
    use Queueable;

    protected ?Model $sender;
    protected Model $message;

    public function __construct()
    {
        $this->sender = Auth::user();
    }

    public function setMessage(Model $message)
    {
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return config('chat.default_notification_channels');
    }

    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'sender_first_name' => $this->sender->first_name,
            'sender_last_name' => $this->sender->last_name
        ]);
    }

    public function toExpoPush(): ExpoMessage
    {
        return ExpoMessage::create()
            ->title("New message from {$this->sender->first_name} {$this->sender->last_name}")
            ->body($this->message->text)
            ->setJsonData([
                'type' => self::class,
                'message' => $this->message->only(['id', 'conversation_id', 'sender_id', 'recipient_id', 'text'])
            ]);
    }
}
