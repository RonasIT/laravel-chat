<?php

use App\Models\User;
use Illuminate\Notifications\Channels\BroadcastChannel;
use RonasIT\Media\Models\Media;

return [
    'classes' => [
        'user_model' => User::class,
        'media_model' => Media::class,
    ],

    'default_channels' => [
        BroadcastChannel::class,
    ],

    'order_by' => [
        'conversation' => ['id', 'created_at', 'updated_at'],
        'message' => ['id', 'created_at'],
    ],
];
