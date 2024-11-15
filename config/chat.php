<?php

use App\Models\User;
use Illuminate\Notifications\Channels\BroadcastChannel;

return [
    'classes' => [
        'user_model' => User::class,
        'media_model' => RonasIT\Media\Models\Media::class,
    ],
    'default_channels' => [
        BroadcastChannel::class
    ],
];
