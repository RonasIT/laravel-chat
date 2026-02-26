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
use RonasIT\Chat\Tests\Support\TableTestState;

class ConversationStaticTest extends TestCase
{
    protected static User $groupCreator;
    protected static User $user;
    protected static User $someAuthUser;
    protected static User $fourthUser;

    protected static ModelTestState $conversationState;
    protected static TableTestState $conversationMemberState;

    public function setUp(): void
    {
        parent::setUp();

        self::$groupCreator ??= User::find(1);
        self::$user ??= User::find(2);
        self::$someAuthUser ??= User::find(3);
        self::$fourthUser ??= User::find(4);

        self::$conversationState = new ModelTestState(Conversation::class);
        self::$conversationMemberState = new TableTestState('conversation_member');
    }

    public function testEverythingDisabledExceptSearch(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationSearch);

        $responseSearch = $this->actingAs(self::$groupCreator)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$groupCreator)->getJson('/conversations/1');
        $responseUpdate = $this->actingAs(self::$groupCreator)->putJson('/conversations/6', ['title' => 'New Title']);
        $responseDelete = $this->actingAs(self::$groupCreator)->deleteJson('/conversations/1');
        $responseGetOrCreatePrivate = $this->actingAs(self::$groupCreator)->postJson('/conversations/private');
        $responseSearchMessages = $this->actingAs(self::$groupCreator)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$groupCreator)->postJson('/messages/1');
        $responseRead = $this->actingAs(self::$groupCreator)->putJson('messages/1/read');

        $responseSearch->assertOk();

        $responseGet->assertNotFound();
        $responseUpdate->assertNotFound();
        $responseDelete->assertNotFound();
        $responseGetOrCreatePrivate->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testEverythingDisabledExceptDelete(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $responseSearch = $this->actingAs(self::$groupCreator)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$groupCreator)->getJson('/conversations/1');
        $responseUpdate = $this->actingAs(self::$groupCreator)->putJson('/conversations/6', ['title' => 'New Title']);
        $responseDelete = $this->actingAs(self::$groupCreator)->deleteJson('/conversations/1');
        $responseGetOrCreatePrivate = $this->actingAs(self::$groupCreator)->postJson('/conversations/private');
        $responseSearchMessages = $this->actingAs(self::$groupCreator)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$groupCreator)->postJson('/messages/1');
        $responseRead = $this->actingAs(self::$groupCreator)->putJson('messages/1/read');

        $responseDelete->assertNoContent();

        $responseGet->assertNotFound();
        $responseUpdate->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetOrCreatePrivate->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testEverythingDisabledExceptGet(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $responseSearch = $this->actingAs(self::$groupCreator)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$groupCreator)->getJson('/conversations/1');
        $responseUpdate = $this->actingAs(self::$groupCreator)->putJson('/conversations/6', ['title' => 'New Title']);
        $responseDelete = $this->actingAs(self::$groupCreator)->deleteJson('/conversations/1');
        $responseGetOrCreatePrivate = $this->actingAs(self::$groupCreator)->postJson('/conversations/private');
        $responseSearchMessages = $this->actingAs(self::$groupCreator)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$groupCreator)->postJson('/messages/1');
        $responseRead = $this->actingAs(self::$groupCreator)->putJson('messages/1/read');

        $responseGet->assertOk();

        $responseUpdate->assertNotFound();
        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetOrCreatePrivate->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testGetPrivate()
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $response = $this->actingAs(self::$groupCreator)->json('get', '/conversations/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_private', $response->json());
    }

    public function testGetGroup()
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $response = $this->actingAs(self::$groupCreator)->json('get', '/conversations/6');

        $response->assertOk();

        $this->assertEqualsFixture('get_group', $response->json());
    }

    public function testGetWithRelations()
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $response = $this->actingAs(self::$groupCreator)->json(
            method: 'get',
            uri: '/conversations/1',
            data: [
                'with' => [
                    'messages',
                    'members',
                    'last_message',
                    'cover',
                ],
            ],
        );

        $response->assertOk();

        $this->assertEqualsFixture('get_with_relations', $response->json());
    }

    public function testGetNonMember()
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $response = $this->actingAs(self::$someAuthUser)->json('get', '/conversations/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testGetNotExists()
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $response = $this->actingAs(self::$groupCreator)->json('get', '/conversations/0');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);
    }

    public function testGetEndpointDisabled()
    {
        $response = $this->actingAs(self::$groupCreator)->json('get', '/conversations/1');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);
    }

    public function testDeletePrivate()
    {
        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $response = $this->actingAs(self::$groupCreator)->json('delete', '/conversations/1');

        $response->assertNoContent();

        Notification::assertSentTo(self::$groupCreator, ConversationDeletedNotification::class);
        Notification::assertSentTo(self::$user, ConversationDeletedNotification::class);

        self::$conversationState->assertChangesEqualsFixture('private_deleted');
    }

    public function testDeleteGroup(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $response = $this->actingAs(self::$groupCreator)->json('delete', '/conversations/6');

        $response->assertNoContent();

        Notification::assertSentTo(self::$groupCreator, ConversationDeletedNotification::class);
        Notification::assertSentTo(self::$user, ConversationDeletedNotification::class);

        self::$conversationState->assertChangesEqualsFixture('group_deleted');
    }

    public function testDeletePrivateNonMember()
    {
        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $response = $this->actingAs(self::$someAuthUser)->json('delete', '/conversations/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$conversationState->assertNotChanged();
    }

    public function testDeleteGroupNonCreator()
    {
        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $response = $this->actingAs(self::$user)->json('delete', '/conversations/6');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$conversationState->assertNotChanged();
    }

    public function testDeleteNotExists()
    {
        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $response = $this->actingAs(self::$groupCreator)->json('delete', '/conversations/0');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);

        self::$conversationState->assertNotChanged();
    }

    public function testDeleteEndpointDisabled()
    {
        $response = $this->actingAs(self::$groupCreator)->json('delete', '/conversations/1');

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
                        'members',
                        'last_message',
                        'cover',
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
            [
                'filter' => [
                    'type' => 'group',
                ],
                'fixture' => 'search_by_type',
            ],
        ];
    }

    #[DataProvider('getSearchFilters')]
    public function testSearch(array $filter, string $fixture)
    {
        Route::chat(ChatRouteActionEnum::ConversationSearch);

        $response = $this->actingAs(self::$groupCreator)->json('get', '/conversations', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testSearchEndpointDisabled()
    {
        $response = $this->actingAs(self::$groupCreator)->json('get', '/conversations');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);
    }

    public function testEverythingDisabledExceptGetOrCreatePrivate(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationGetOrCreatePrivate);

        $responseSearch = $this->actingAs(self::$groupCreator)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$groupCreator)->getJson('/conversations/1');
        $responseUpdate = $this->actingAs(self::$groupCreator)->putJson('/conversations/6', ['title' => 'New Title']);
        $responseDelete = $this->actingAs(self::$groupCreator)->deleteJson('/conversations/1');
        $responseGetOrCreatePrivate = $this->actingAs(self::$groupCreator)->postJson('/conversations/private', ['participant_id' => 2]);
        $responseSearchMessages = $this->actingAs(self::$groupCreator)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$groupCreator)->postJson('/messages/1');
        $responseRead = $this->actingAs(self::$groupCreator)->putJson('messages/1/read');

        $responseGetOrCreatePrivate->assertOk();

        $responseSearch->assertNotFound();
        $responseGet->assertNotFound();
        $responseUpdate->assertNotFound();
        $responseDelete->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testGetOrCreatePrivateExists(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationGetOrCreatePrivate);

        $response = $this->actingAs(self::$groupCreator)->json('post', '/conversations/private', ['participant_id' => 2]);

        $response->assertOk();

        $this->assertEqualsFixture('get_or_create_private_exists', $response->json());

        self::$conversationState->assertNotChanged();
        self::$conversationMemberState->assertNotChanged();
    }

    public function testGetOrCreatePrivateCreatesNew(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationGetOrCreatePrivate);

        $response = $this->actingAs(self::$groupCreator)->json('post', '/conversations/private', ['participant_id' => 4]);

        $response->assertOk();

        $this->assertEqualsFixture('get_or_create_private_created_response', $response->json());

        self::$conversationState->assertChangesEqualsFixture('get_or_create_private_created');
        self::$conversationMemberState->assertChangesEqualsFixture('get_or_create_private_created');
    }

    public function testGetOrCreatePrivateSelf(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationGetOrCreatePrivate);

        $response = $this->actingAs(self::$groupCreator)->json('post', '/conversations/private', ['participant_id' => 1]);

        $response->assertUnprocessable();

        $response->assertJson(['message' => 'You cannot start a conversation with yourself.']);

        self::$conversationState->assertNotChanged();
        self::$conversationMemberState->assertNotChanged();
    }

    public function testGetOrCreatePrivateEndpointDisabled(): void
    {
        $response = $this->actingAs(self::$groupCreator)->json('post', '/conversations/private', ['participant_id' => 2]);

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);
    }

    public function testEverythingDisabledExceptUpdate(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationUpdate);

        $responseSearch = $this->actingAs(self::$groupCreator)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$groupCreator)->getJson('/conversations/1');
        $responseUpdate = $this->actingAs(self::$groupCreator)->putJson('/conversations/6', ['title' => 'New Title', 'member_ids' => [1, 3]]);
        $responseDelete = $this->actingAs(self::$groupCreator)->deleteJson('/conversations/1');
        $responseGetOrCreatePrivate = $this->actingAs(self::$groupCreator)->postJson('/conversations/private');
        $responseSearchMessages = $this->actingAs(self::$groupCreator)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$groupCreator)->postJson('/messages/1');
        $responseRead = $this->actingAs(self::$groupCreator)->putJson('messages/1/read');

        $responseUpdate->assertNoContent();

        $responseSearch->assertNotFound();
        $responseGet->assertNotFound();
        $responseDelete->assertNotFound();
        $responseGetOrCreatePrivate->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testUpdate(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationUpdate);

        $response = $this->actingAs(self::$groupCreator)->json('put', '/conversations/6', [
            'title' => 'New Title',
            'member_ids' => [1, 3, 4],
            'cover_id' => 2,
        ]);

        $response->assertNoContent();

        self::$conversationState->assertChangesEqualsFixture('updated');
        self::$conversationMemberState->assertChangesEqualsFixture('updated');
    }

    public function testUpdateByNonCreator(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationUpdate);

        $response = $this->actingAs(self::$user)->json('put', '/conversations/6');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$conversationState->assertNotChanged();
        self::$conversationMemberState->assertNotChanged();
    }

    public function testUpdateNotFound(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationUpdate);

        $response = $this->actingAs(self::$groupCreator)->json('put', '/conversations/0');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);

        self::$conversationState->assertNotChanged();
        self::$conversationMemberState->assertNotChanged();
    }

    public function testUpdateEndpointDisabled(): void
    {
        $response = $this->actingAs(self::$groupCreator)->json('put', '/conversations/6');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);
    }
}
