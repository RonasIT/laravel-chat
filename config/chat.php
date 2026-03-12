<?php

use App\Models\User;
use Illuminate\Notifications\Channels\BroadcastChannel;
use RonasIT\Media\Models\Media;

return [
    'classes' => [
        'user' => [
            // The User model class used for conversation members, message senders, and auth.
            'model' => User::class,

            'columns' => [
                // The column in the users table used as the display name for private conversation titles.
                'name' => 'name',

                // The column in the users table referencing the user's avatar media record.
                // Used to resolve the cover for private conversations.
                'avatar_id' => 'avatar_id',
            ],
        ],

        'media' => [
            // The Media model class used for conversation covers and message attachments.
            'model' => Media::class,
        ],
    ],

    // Notification channels used to broadcast chat events (new messages, deleted conversations).
    'default_channels' => [
        BroadcastChannel::class,
    ],
];
