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

    'exceptions' => [
        'self_message' => 'You cannot send a message to yourself.',
        'not_message_recipient' => 'You are not the recipient of this message.',
        'not_found' => ':entity does not exist',
        'not_owner' => 'You are not the owner of this Conversation.',
    ],
];
