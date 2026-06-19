<?php

namespace RonasIT\Chat\Tests\Models;

use RonasIT\Chat\Models\Conversation;

class CustomConversation extends Conversation
{
    protected $table = 'conversations';

    public function getForeignKey(): string
    {
        return 'conversation_id';
    }

    public function getTitleAttribute(?string $value): ?string
    {
        return $value . '_custom';
    }
}
