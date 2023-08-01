<?php

use App\Models\User;
use App\Models\Media;
use Illuminate\Notifications\Channels\BroadcastChannel;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;

return [
    'classes' => [
        'user_model' => User::class,
        'media_model' => Media::class,
    ],
    'default_notification_channels' => [
        ExpoChannel::class,
        BroadcastChannel::class,
    ],
];
