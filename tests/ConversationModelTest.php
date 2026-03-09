<?php

namespace RonasIT\Chat\Tests;

use RonasIT\Chat\Enums\Conversation\TypeEnum;
use RonasIT\Chat\Models\Conversation;

class ConversationModelTest extends TestCase
{
    public function testIsPrivateReturnsTrueForPrivateConversation()
    {
        $conversation = new Conversation(['type' => TypeEnum::Private]);

        $this->assertTrue($conversation->isPrivate());
        $this->assertFalse($conversation->isGroup());
    }

    public function testIsGroupReturnsTrueForGroupConversation()
    {
        $conversation = new Conversation(['type' => TypeEnum::Group]);

        $this->assertTrue($conversation->isGroup());
        $this->assertFalse($conversation->isPrivate());
    }
}
