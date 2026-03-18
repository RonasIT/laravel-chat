<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'custom' => [
        'recipient_same_as_sender' => 'The recipient id must not be the same as the message sender id.',
        'message_not_pinned' => 'Message is not pinned.',
    ],

    'exceptions' => [
        'self_message' => 'You cannot send a message to yourself.',
        'not_found' => ':entity does not exist',
    ],
];
