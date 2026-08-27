<?php

namespace RonasIT\Chat\Tests;

use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Models\Message;

class ConversationRelationsOrderTest extends TestCase
{
    public function testMessagesAreOrderedById()
    {
        $conversation = Conversation::query()->findOrFail(1);

        $this->assertEquals([3, 5, 9], $conversation->messages()->pluck('messages.id')->all());
    }

    public function testEagerLoadedMessagesAreOrderedById()
    {
        $conversation = Conversation::query()->with('messages')->findOrFail(1);

        $this->assertEquals([3, 5, 9], $conversation->messages->pluck('id')->all());
    }

    public function testMembersAreOrderedByPivotId()
    {
        $conversation = Conversation::query()->findOrFail(1);

        $this->assertEquals([2, 3, 1], $conversation->members()->pluck('users.id')->all());
    }

    public function testReadsAreOrderedById()
    {
        $message = Message::query()->findOrFail(3);

        $this->assertEquals([2, 4, 7], $message->reads()->pluck('read_messages.id')->all());
    }

    public function testLastMessageIsResolvedDeterministicallyOnEqualCreatedAt()
    {
        $conversation = Conversation::query()->with('last_message')->findOrFail(1);

        $this->assertEquals(9, $conversation->last_message->id);
    }

    public function testRelationOrderCanBeOverriddenWithReorder()
    {
        $conversation = Conversation::query()->findOrFail(1);

        $this->assertEquals([9, 5, 3], $conversation->messages()->reorder('messages.id', 'desc')->pluck('messages.id')->all());
    }
}
