<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Attributes\DefineRoute;
use PHPUnit\Framework\Attributes\DataProvider;
use RonasIT\Chat\Enums\ChatRouteActionEnum;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Tests\Models\User;
use RonasIT\Chat\Tests\Support\ModelTestState;
use RonasIT\Chat\Tests\Support\TableTestState;

class ConversationStaticTest extends TestCase
{
    protected static User $sender;
    protected static User $recipient;
    protected static User $someAuthUser;

    protected static ModelTestState $conversationState;
    protected static TableTestState $conversationMemberState;

    public function setUp(): void
    {
        parent::setUp();

        self::$sender ??= User::find(1);
        self::$recipient ??= User::find(2);
        self::$someAuthUser ??= User::find(3);

        self::$conversationState = new ModelTestState(Conversation::class);
        self::$conversationMemberState = new TableTestState('conversation_member');
    }

    protected function defineConversationsSearchRoute($router): void
    {
        Route::chat(ChatRouteActionEnum::ConversationsSearch);
    }

    protected function defineConversationDeleteRoute($router): void
    {
        Route::chat(ChatRouteActionEnum::ConversationDelete);
    }

    protected function defineConversationGetRoute($router): void
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);
    }

    protected function defineConversationGetByUserRoute($router): void
    {
        Route::chat(ChatRouteActionEnum::ConversationGetByUser);
    }

    #[DefineRoute('defineConversationsSearchRoute')]
    public function testEverythingDisabledExceptSearch(): void
    {
        $responseSearch = $this->actingAs(self::$sender)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$sender)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$sender)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$sender)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$sender)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$sender)->postJson('/messages');

        $responseSearch->assertOk();

        $responseGet->assertNotFound();
        $responseDelete->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
    }

    #[DefineRoute('defineConversationDeleteRoute')]
    public function testEverythingDisabledExceptDelete(): void
    {
        $responseSearch = $this->actingAs(self::$sender)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$sender)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$sender)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$sender)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$sender)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$sender)->postJson('/messages');

        $responseDelete->assertNoContent();

        $responseGet->assertMethodNotAllowed();

        $responseSearch->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
    }

    #[DefineRoute('defineConversationGetRoute')]
    public function testEverythingDisabledExceptGet(): void
    {
        $responseSearch = $this->actingAs(self::$sender)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$sender)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$sender)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$sender)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$sender)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$sender)->postJson('/messages');

        $responseGet->assertOk();

        $responseDelete->assertMethodNotAllowed();

        $responseSearch->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
    }

    #[DefineRoute('defineConversationGetByUserRoute')]
    public function testEverythingDisabledExceptGetByUser(): void
    {
        $responseSearch = $this->actingAs(self::$sender)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$sender)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$sender)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$sender)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$sender)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$sender)->postJson('/messages');

        $responseGetByUser->assertOk();

        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGet->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
    }

    #[DefineRoute('defineConversationGetRoute')]
    public function testGetBySender()
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation_by_sender', $response->json());
    }

    #[DefineRoute('defineConversationGetRoute')]
    public function testGetWithRelations()
    {
        $response = $this->actingAs(self::$sender)->json(
            method: 'get',
            uri: '/conversations/1',
            data: [
                'with' => [
                    'messages',
                    'creator',
                    'members',
                    'last_message',
                    'cover',
                    'pinned_messages',
                ],
                'with_count' => [
                    'members',
                ],
            ],
        );

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation_with_relations', $response->json());
    }

    #[DefineRoute('defineConversationGetRoute')]
    public function testGetByRecipient()
    {
        $response = $this->actingAs(self::$recipient)->json('get', '/conversations/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation_by_recipient', $response->json());
    }

    #[DefineRoute('defineConversationGetRoute')]
    public function testGetBySomeUser()
    {
        $response = $this->actingAs(self::$someAuthUser)->json('get', '/conversations/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    #[DefineRoute('defineConversationGetRoute')]
    public function testGetNotExists()
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations/0');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);
    }

    #[DefineRoute('defineConversationGetRoute')]
    public function testGetWithInvalidId(): void
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations/abc');

        $response->assertNotFound();
    }

    #[DefineRoute('defineConversationsSearchRoute')]
    public function testGetEndpointDisabled()
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations/1');

        $response->assertNotFound();
    }

    #[DefineRoute('defineConversationGetByUserRoute')]
    public function testGetBetweenUsersIdBySender()
    {
        $response = $this->actingAs(self::$sender)->json('get', 'users/2/conversation');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation_between_users_by_sender', $response->json());
    }

    #[DefineRoute('defineConversationGetByUserRoute')]
    public function testGetBetweenUsersByRecipient()
    {
        $response = $this->actingAs(self::$recipient)->json('get', 'users/1/conversation');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation_between_users_by_recipient', $response->json());
    }

    #[DefineRoute('defineConversationGetByUserRoute')]
    public function testGetBetweenUsersIdWithRelations()
    {
        $response = $this->actingAs(self::$sender)->json('get', 'users/2/conversation', [
            'with' => [
                'messages',
                'creator',
                'members',
                'last_message',
                'cover',
                'pinned_messages',
            ],
            'with_count' => [
                'members',
            ],
        ]);

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation_with_relations', $response->json());
    }

    #[DefineRoute('defineConversationGetByUserRoute')]
    public function testGetBetweenUsersWhoDontHaveConversations()
    {
        $response = $this->actingAs(self::$sender)->json('get', 'users/3/conversation');

        $response->assertNoContent();
    }

    #[DefineRoute('defineConversationGetByUserRoute')]
    public function testGetBetweenUsersWithInvalidUserId(): void
    {
        $response = $this->actingAs(self::$sender)->json('get', '/users/abc/conversation');

        $response->assertNotFound();
    }

    #[DefineRoute('defineConversationsSearchRoute')]
    public function testGetByUserEndpointDisabled()
    {
        $response = $this->actingAs(self::$sender)->json('get', 'users/2/conversation');

        $response->assertNotFound();
    }

    #[DefineRoute('defineConversationDeleteRoute')]
    public function testDeleteBySender()
    {
        $response = $this->actingAs(self::$sender)->json('delete', '/conversations/1');

        $response->assertNoContent();

        $this->assertBroadcastNotificationSent('delete_by_sender');

        self::$conversationState->assertChangesEqualsFixture('deleted');
        self::$conversationMemberState->assertChangesEqualsFixture('deleted');
    }

    #[DefineRoute('defineConversationDeleteRoute')]
    public function testDeleteByRecipient()
    {
        $response = $this->actingAs(self::$recipient)->json('delete', '/conversations/1');

        $this->assertBroadcastNotificationSent('delete_by_recipient');

        $response->assertNoContent();

        self::$conversationState->assertChangesEqualsFixture('deleted');
        self::$conversationMemberState->assertChangesEqualsFixture('deleted');
    }

    #[DefineRoute('defineConversationDeleteRoute')]
    public function testDeleteBySomeUser()
    {
        $response = $this->actingAs(self::$someAuthUser)->json('delete', '/conversations/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$conversationState->assertNotChanged();
    }

    #[DefineRoute('defineConversationDeleteRoute')]
    public function testDeleteNotExists()
    {
        $response = $this->actingAs(self::$sender)->json('delete', '/conversations/0');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);

        self::$conversationState->assertNotChanged();
    }

    #[DefineRoute('defineConversationDeleteRoute')]
    public function testDeleteWithInvalidId(): void
    {
        $response = $this->actingAs(self::$sender)->json('delete', '/conversations/abc');

        $response->assertNotFound();

        self::$conversationState->assertNotChanged();
    }

    #[DefineRoute('defineConversationDeleteRoute')]
    public function testDeleteGroupByCreator()
    {
        $response = $this->actingAs(self::$sender)->json('delete', '/conversations/6');

        $response->assertNoContent();

        $this->assertBroadcastNotificationSent('delete_group_by_creator');

        self::$conversationState->assertChangesEqualsFixture('deleted_group');
        self::$conversationMemberState->assertChangesEqualsFixture('deleted_group');
    }

    #[DefineRoute('defineConversationDeleteRoute')]
    public function testDeleteGroupByNonCreator()
    {
        $response = $this->actingAs(self::$recipient)->json('delete', '/conversations/6');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$conversationState->assertNotChanged();
    }

    #[DefineRoute('defineConversationsSearchRoute')]
    public function testDeleteEndpointDisabled()
    {
        $response = $this->actingAs(self::$sender)->json('delete', '/conversations/1');

        $response->assertNotFound();
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
                        'creator',
                        'members',
                        'last_message',
                        'cover',
                        'pinned_messages',
                    ],
                    'with_count' => [
                        'members',
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
                    'order_by' => 'id',
                    'desc' => true,
                ],
                'fixture' => 'search_by_order_by_desc',
            ],
            [
                'filter' => [
                    'order_by' => 'last_updated_at',
                    'desc' => true,
                ],
                'fixture' => 'search_order_by_last_updated_at',
            ],
            [
                'filter' => ['with_unread_messages_count' => true],
                'fixture' => 'search_with_unread_messages_count',
            ],
            [
                'filter' => ['type' => 'private'],
                'fixture' => 'search_by_type_private',
            ],
            [
                'filter' => ['type' => 'group'],
                'fixture' => 'search_by_type_group',
            ],
            [
                'filter' => [
                    'type' => 'private',
                    'with' => ['cover'],
                ],
                'fixture' => 'search_private_with_overridden_fields',
            ],
        ];
    }

    #[DataProvider('getSearchFilters')]
    #[DefineRoute('defineConversationsSearchRoute')]
    public function testSearch(array $filter, string $fixture)
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }

    #[DefineRoute('defineConversationsSearchRoute')]
    public function testSearchWithInvalidOrderBy()
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations', [
            'order_by' => 'invalid_field',
        ]);

        $response->assertUnprocessable();
    }

    #[DefineRoute('defineConversationGetRoute')]
    public function testSearchEndpointDisabled()
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations');

        $response->assertNotFound();
    }
}
