<?php

namespace RonasIT\Chat\Tests;

use RonasIT\Chat\Contracts\Models\ConversationModelContract;
use RonasIT\Chat\Contracts\Models\MessageModelContract;
use RonasIT\Chat\Tests\Models\CustomConversation;
use RonasIT\Chat\Tests\Models\CustomMessage;
use RonasIT\Chat\Tests\Models\User;

class ModelBindingTest extends TestCase
{
    protected static User $firstUser;

    public function setUp(): void
    {
        parent::setUp();

        self::$firstUser ??= User::find(1);

        $this->app->bind(ConversationModelContract::class, CustomConversation::class);
        $this->app->bind(MessageModelContract::class, CustomMessage::class);

        $this->app->alias(CustomConversation::class, ConversationModelContract::class);
        $this->app->alias(CustomMessage::class, MessageModelContract::class);
    }

    public function testGetConversationWithCustomModel(): void
    {
        $response = $this->actingAs(self::$firstUser)->json('get', '/conversations/1', [
            'with' => [
                'messages',
            ],
        ]);

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation_with_custom_model', $response->json(), 2);
    }

    public function testSearchMessagesWithCustomModel(): void
    {
        $response = $this->actingAs(self::$firstUser)->getJson('/conversations/1/messages');

        $response->assertOk();

        $this->assertEqualsFixture('search_messages_with_custom_model', $response->json());
    }
}
