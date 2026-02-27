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
        'not_found' => ':entity does not exist',
        'not_creator' => 'You are not the creator of this Conversation.',
        'not_conversation_member' => 'You are not a member of this conversation.',
    ],
];
