<?php

namespace RonasIT\Chat\Enums\Conversation;

use RonasIT\Support\Traits\EnumTrait;

enum TypeEnum: string
{
    use EnumTrait;

    case Private = 'private';
    case Group = 'group';
}
