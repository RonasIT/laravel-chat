<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use RonasIT\Chat\Enums\ChatRouteActionEnum;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Notifications\ConversationDeletedNotification;
use RonasIT\Chat\Tests\Models\User;
use RonasIT\Chat\Tests\Support\ModelTestState;

class ConversationStaticTest extends TestCase
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

    public function testEverythingDisabledExceptSearch(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationSearch);

        $responseSearch = $this->actingAs(self::$sender)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$sender)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$sender)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$sender)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$sender)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$sender)->postJson('/messages');
        $responseRead = $this->actingAs(self::$sender)->putJson('messages/1/read');

        $responseSearch->assertOk();

        $responseGet->assertNotFound();
        $responseDelete->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testEverythingDisabledExceptDelete(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $responseSearch = $this->actingAs(self::$sender)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$sender)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$sender)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$sender)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$sender)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$sender)->postJson('/messages');
        $responseRead = $this->actingAs(self::$sender)->putJson('messages/1/read');

        $responseDelete->assertNoContent();

        $responseGet->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testEverythingDisabledExceptGet(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $responseSearch = $this->actingAs(self::$sender)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$sender)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$sender)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$sender)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$sender)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$sender)->postJson('/messages');
        $responseRead = $this->actingAs(self::$sender)->putJson('messages/1/read');

        $responseGet->assertOk();

        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testEverythingDisabledExceptGetByUser(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationGetByUser);

        $responseSearch = $this->actingAs(self::$sender)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$sender)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$sender)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$sender)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$sender)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$sender)->postJson('/messages');
        $responseRead = $this->actingAs(self::$sender)->putJson('messages/1/read');

        $responseGetByUser->assertOk();

        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGet->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testGetBySender()
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $response = $this->actingAs(self::$sender)->json('get', '/conversations/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation', $response->json());
    }

    public function testGetWithRelations()
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

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
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $response = $this->actingAs(self::$sender)->json('get', '/conversations/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation', $response->json());
    }

    public function testGetBySomeUser()
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $response = $this->actingAs(self::$someAuthUser)->json('get', '/conversations/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'You are not the owner of this Conversation.']);
    }

    public function testGetNotExists()
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $response = $this->actingAs(self::$sender)->json('get', '/conversations/0');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);
    }

    public function testGetGetDisabled()
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations/1');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);
    }

    public function testGetBetweenUsersIdBySender()
    {
        Route::chat(ChatRouteActionEnum::ConversationGetByUser);

        $response = $this->actingAs(self::$sender)->json('get', 'users/2/conversation');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation', $response->json());
    }

    public function testGetBetweenUsersByRecipient()
    {
        Route::chat(ChatRouteActionEnum::ConversationGetByUser);

        $response = $this->actingAs(self::$recipient)->json('get', 'users/1/conversation');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation', $response->json());
    }

    public function testGetBetweenUsersWhoDontHaveConversations()
    {
        Route::chat(ChatRouteActionEnum::ConversationGetByUser);

        $response = $this->actingAs(self::$recipient)->json('get', 'users/3/conversation');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);
    }

    public function testGetByUserGetByUserDisabled()
    {
        $response = $this->actingAs(self::$sender)->json('get', 'users/2/conversation');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);
    }

    public function testDeleteBySender()
    {
        Notification::fake();

        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $response = $this->actingAs(self::$sender)->json('delete', '/conversations/1');

        $response->assertNoContent();

        Notification::assertSentTo(self::$recipient, ConversationDeletedNotification::class);

        self::$conversationState->assertChangesEqualsFixture('deleted');
    }

    public function testDeleteByRecipient()
    {
        Notification::fake();

        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $response = $this->actingAs(self::$recipient)->json('delete', '/conversations/1');

        Notification::assertSentTo(self::$sender, ConversationDeletedNotification::class);

        $response->assertNoContent();

        self::$conversationState->assertChangesEqualsFixture('deleted');
    }

    public function testDeleteBySomeUser()
    {
        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $response = $this->actingAs(self::$someAuthUser)->json('delete', '/conversations/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'You are not the owner of this Conversation.']);

        self::$conversationState->assertNotChanged();
    }

    public function testDeleteNotExists()
    {
        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $response = $this->actingAs(self::$sender)->json('delete', '/conversations/0');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);

        self::$conversationState->assertNotChanged();
    }

    public function testDeleteDeleteDisabled()
    {
        $response = $this->actingAs(self::$sender)->json('delete', '/conversations/1');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);
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
        Route::chat(ChatRouteActionEnum::ConversationSearch);

        $response = $this->actingAs(self::$sender)->json('get', '/conversations', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testSearchSearchDisabled()
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);
    }
}
