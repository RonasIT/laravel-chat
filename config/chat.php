<?php

use App\Models\User;
use RonasIT\Media\Models\Media;
use Illuminate\Notifications\Channels\BroadcastChannel;

return [
    'classes' => [
        'user_model' => User::class,
        'media_model' => Media::class,
    ],
    'default_channels' => [
        BroadcastChannel::class
    ],
];