<?php

use App\Models\User;
use Illuminate\Notifications\Channels\BroadcastChannel;

return [
    'classes' => [
        'user_model' => User::class,
    ],
    'default_channels' => [
        BroadcastChannel::class
    ],
];
