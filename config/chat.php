<?php

use App\Models\User;
use Illuminate\Notifications\Channels\BroadcastChannel;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Models\Message;
use RonasIT\Media\Models\Media;

return [
    'classes' => [
        'user_model' => User::class,
        'media_model' => Media::class,
        'conversation_model' => Conversation::class,
        'message_model' => Message::class,
    ],

    'default_channels' => [
        BroadcastChannel::class,
    ],
];
