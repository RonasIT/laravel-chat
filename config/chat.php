<?php

use App\Models\User;
use Illuminate\Notifications\Channels\BroadcastChannel;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;

return [
    'classes' => [
        'user_model' => User::class,
    ],
    'notification_channels' => [
        ExpoChannel::class,
        BroadcastChannel::class,
    ],
];
