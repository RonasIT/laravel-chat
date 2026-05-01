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
                // The columns list used as the display name for private conversation titles.
                'full_name' => [
                    'name',
                ],
                // Separators between full_name columns.
                // A separator per gap (last value repeats if fewer than gaps) — e.g., [' ', ' - '] → "Smith John - Developer".
                'full_name_separator' => [],

                // Used to resolve the cover for private conversations.
                'avatar' => 'avatar_id',
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
