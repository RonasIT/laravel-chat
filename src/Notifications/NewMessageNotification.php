<?php

namespace RonasIT\Chat\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Channels\BroadcastChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;
use RonasIT\Chat\Models\Message;
use RonasIT\Chat\Contracts\Models\UserContract;

class NewMessageNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    protected UserContract $sender;
    protected Message $message;

    public function __construct(Message $message, $sender)
    {
        $this->message = $message;
        $this->sender = $sender;
    }

    public function via($notifiable): array
    {
        return [
            BroadcastChannel::class,
            ExpoChannel::class
        ];
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
