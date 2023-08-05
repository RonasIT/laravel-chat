<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Channels\BroadcastChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ConversationDeletedNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    protected array $conversation;

    public function __construct($conversation)
    {
        $this->conversation = $conversation;
    }

    public function via($notifiable): array
    {
        return [BroadcastChannel::class];
    }

    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage(['conversation' => $this->conversation]);
    }
}