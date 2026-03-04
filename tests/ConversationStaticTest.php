<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
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

    public function testEverythingDisabledExceptSearch(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationsSearch);

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

    public function testEverythingDisabledExceptDelete(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $responseSearch = $this->actingAs(self::$sender)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$sender)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$sender)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$sender)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$sender)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$sender)->postJson('/messages');

        $responseDelete->assertNoContent();

        $responseGet->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
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

        $responseGet->assertOk();

        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
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

        $responseGetByUser->assertOk();

        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGet->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
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
                    'creator',
                    'members',
                    'last_message',
                    'cover',
                    'pinned_messages',
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

        $response->assertJson(['message' => 'You are not a member of this conversation.']);
    }

    public function testGetNotExists()
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $response = $this->actingAs(self::$sender)->json('get', '/conversations/0');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);
    }

    public function testGetEndpointDisabled()
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

    public function testGetBetweenUsersIdWithRelations()
    {
        Route::chat(ChatRouteActionEnum::ConversationGetByUser);

        $response = $this->actingAs(self::$sender)->json('get', 'users/2/conversation', [
            'with' => [
                'messages',
                'creator',
                'members',
                'last_message',
                'cover',
                'pinned_messages',
            ],
        ]);

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation_with_relations', $response->json());
    }

    public function testGetBetweenUsersWhoDontHaveConversations()
    {
        Route::chat(ChatRouteActionEnum::ConversationGetByUser);

        $response = $this->actingAs(self::$sender)->json('get', 'users/3/conversation');

        $response->assertNoContent();
    }

    public function testGetByUserEndpointDisabled()
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

        $this->assertBroadcastNotificationSent('delete_by_sender');

        self::$conversationState->assertChangesEqualsFixture('deleted');
        self::$conversationMemberState->assertChangesEqualsFixture('deleted');
    }

    public function testDeleteByRecipient()
    {
        Notification::fake();

        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $response = $this->actingAs(self::$recipient)->json('delete', '/conversations/1');

        $this->assertBroadcastNotificationSent('delete_by_recipient');

        $response->assertNoContent();

        self::$conversationState->assertChangesEqualsFixture('deleted');
        self::$conversationMemberState->assertChangesEqualsFixture('deleted');
    }

    public function testDeleteBySomeUser()
    {
        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $response = $this->actingAs(self::$someAuthUser)->json('delete', '/conversations/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

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

    public function testDeleteGroupByCreator()
    {
        Notification::fake();

        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $response = $this->actingAs(self::$sender)->json('delete', '/conversations/6');

        $response->assertNoContent();

        $this->assertBroadcastNotificationSent('delete_group_by_creator');

        self::$conversationState->assertChangesEqualsFixture('deleted_group');
        self::$conversationMemberState->assertChangesEqualsFixture('deleted_group');
    }

    public function testDeleteGroupByNonCreator()
    {
        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $response = $this->actingAs(self::$recipient)->json('delete', '/conversations/6');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$conversationState->assertNotChanged();
    }

    public function testDeleteEndpointDisabled()
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
                        'creator',
                        'members',
                        'last_message',
                        'cover',
                        'pinned_messages',
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
                'filter' => ['with_unread_messages_count' => true],
                'fixture' => 'search_with_unread_messages_count',
            ],
        ];
    }

    #[DataProvider('getSearchFilters')]
    public function testSearch(array $filter, string $fixture)
    {
        Route::chat(ChatRouteActionEnum::ConversationsSearch);

        $response = $this->actingAs(self::$sender)->json('get', '/conversations', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testSearchEndpointDisabled()
    {
        $response = $this->actingAs(self::$sender)->json('get', '/conversations');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);
    }
}
