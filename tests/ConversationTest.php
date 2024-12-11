<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Notifications\ConversationDeletedNotification;
use RonasIT\Chat\Notifications\NewMessageNotification;
use RonasIT\Chat\Tests\Models\User;
use RonasIT\Chat\Tests\Support\ModelTestState;

class ConversationTest extends TestCase
{
    protected static User $sender;
    protected static User $recipient;
    protected static User $someAuthUser;

    protected static ModelTestState $conversationTestState;

    public function setUp(): void
    {
        parent::setUp();

        self::$sender ??= User::find(1);
        self::$recipient ??= User::find(2);
        self::$someAuthUser ??= User::find(3);

        self::$conversationTestState = new ModelTestState(Conversation::class);
    }

    public function testGetBySender()
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation.json', $response->json());
    }

    public function testGetByRecipient()
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation.json', $response->json());
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

        $this->assertEqualsFixture('get_conversation.json', $response->json());
    }

    public function testGetBetweenUsersByRecipient()
    {
        $response = $this->actingAs(self::$recipient)->json('get', 'users/1/conversation');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation.json', $response->json());
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
        $response = $this->actingAs(self::$sender)->json('delete', '/conversations/1');

        $response->assertNoContent();

        Notification::fake();

        self::$recipient->notify(new ConversationDeletedNotification());

        Notification::assertSentTo(self::$recipient, ConversationDeletedNotification::class);

        $response->assertNoContent();

        self::$conversationTestState->assertChangesEqualsFixture('conversation_deleted_conversations_state.json');
    }

    public function testDeleteByRecipient()
    {
        $response = $this->actingAs(self::$recipient)->json('delete', '/conversations/1');

        Notification::fake();

        self::$sender->notify(new ConversationDeletedNotification());

        Notification::assertSentTo(self::$sender, ConversationDeletedNotification::class);

        $response->assertNoContent();

        self::$conversationTestState->assertChangesEqualsFixture('conversation_deleted_conversations_state.json');
    }

    public function testDeleteBySomeUser()
    {
        $response = $this->actingAs(self::$someAuthUser)->json('delete', '/conversations/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'You are not the owner of this Conversation.']);

        self::$conversationTestState->assertNotChanged();
    }

    public function testDeleteNoAuth()
    {
        $response = $this->json('delete', '/conversations/1');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);

        self::$conversationTestState->assertNotChanged();
    }

    public function testDeleteNotExists()
    {
        $response = $this->actingAs(self::$sender)->json('delete', '/conversations/0');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);

        self::$conversationTestState->assertNotChanged();
    }

    public static function getSearchFilters(): array
    {
        return [
            [
                'filter' => ['all' => true],
                'fixture' => 'search_all.json',
            ],
        ];
    }

    #[DataProvider('getSearchFilters')]
    public function testSearch($filter,$fixture)
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }
}