<?php

namespace RonasIT\Chat\Tests\Models;

use RonasIT\Chat\Models\Message;

class CustomMessage extends Message
{
    protected $table = 'messages';

    public function getTextAttribute(string $value): ?string
    {
        return $value . '_custom';
    }
}

