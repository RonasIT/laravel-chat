<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Notifications\ConversationDeletedNotification;
use RonasIT\Chat\Tests\Models\User;
use RonasIT\Chat\Tests\Support\ModelTestState;

class ConversationTest extends TestCase
{
    protected static User $sender;
    protected static User $recipient;
    protected static User $someAuthUser;

    protected static ModelTestState $conversationState;

    public function setUp(): void
    {
        parent::setUp();

        self::$sender ??= User::find(1);
        self::$recipient ??= User::find(2);
        self::$someAuthUser ??= User::find(3);

        self::$conversationState = new ModelTestState(Conversation::class);
    }

    public function testGetBySender()
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation', $response->json());
    }

    public function testGetWithRelations()
    {
        $response = $this->actingAs(self::$sender)->json(
            method: 'get',
            uri: '/conversations/1',
            data: [
                'with' => [
                    'messages',
                    'sender',
                    'recipient',
                    'last_message',
                ],
            ],
        );

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation_with_relations', $response->json());
    }

    public function testGetByRecipient()
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation', $response->json());
    }

    public function testGetBySomeUser()
    {
        $response = $this->actingAs(self::$someAuthUser)->json('get', '/conversations/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'You are not the owner of this Conversation.']);
    }

    public function testGetNoAuth()
    {
        $response = $this->json('get', '/conversations/1');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations/0');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);
    }

    public function testGetBetweenUsersIdBySender()
    {
        $response = $this->actingAs(self::$sender)->json('get', 'users/2/conversation');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation', $response->json());
    }

    public function testGetBetweenUsersByRecipient()
    {
        $response = $this->actingAs(self::$recipient)->json('get', 'users/1/conversation');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation', $response->json());
    }

    public function testGetBetweenUsersWhoDontHaveConversations()
    {
        $response = $this->actingAs(self::$recipient)->json('get', 'users/3/conversation');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);
    }

    public function testGetBetweenAuthAndNoAuthUsers()
    {
        $response = $this->json('get', 'users/1/conversation');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testDeleteBySender()
    {
        Notification::fake();

        $response = $this->actingAs(self::$sender)->json('delete', '/conversations/1');

        $response->assertNoContent();

        Notification::assertSentTo(self::$recipient, ConversationDeletedNotification::class);

        self::$conversationState->assertChangesEqualsFixture('deleted');
    }

    public function testDeleteByRecipient()
    {
        Notification::fake();

        $response = $this->actingAs(self::$recipient)->json('delete', '/conversations/1');

        Notification::assertSentTo(self::$sender, ConversationDeletedNotification::class);

        $response->assertNoContent();

        self::$conversationState->assertChangesEqualsFixture('deleted');
    }

    public function testDeleteBySomeUser()
    {
        $response = $this->actingAs(self::$someAuthUser)->json('delete', '/conversations/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'You are not the owner of this Conversation.']);

        self::$conversationState->assertNotChanged();
    }

    public function testDeleteNoAuth()
    {
        $response = $this->json('delete', '/conversations/1');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);

        self::$conversationState->assertNotChanged();
    }

    public function testDeleteNotExists()
    {
        $response = $this->actingAs(self::$sender)->json('delete', '/conversations/0');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);

        self::$conversationState->assertNotChanged();
    }

    public static function getSearchFilters(): array
    {
        return [
            [
                'filter' => ['all' => true],
                'fixture' => 'search_all',
            ],
            [
                'filter' => [
                    'with' => [
                        'messages',
                        'sender',
                        'recipient',
                        'last_message',
                    ],
                ],
                'fixture' => 'search_with_relations',
            ],
            [
                'filter' => [
                    'page' => 2,
                    'per_page' => 2,
                ],
                'fixture' => 'search_page_per_page',
            ],
            [
                'filter' => [
                    'with_unread_messages_count' => true,
                ],
                'fixture' => 'search_with_unread_messages_count',
            ],
            [
                'filter' => [
                    'order_by' => 'id',
                    'desc' => true,
                ],
                'fixture' => 'search_by_order_by_desc',
            ],
        ];
    }

    #[DataProvider('getSearchFilters')]
    public function testSearch(array $filter, string $fixture)
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testSearchNoAuth()
    {
        $response = $this->json('get', '/conversations');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}