<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use RonasIT\Chat\ChatRouter;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Notifications\ConversationDeletedNotification;
use RonasIT\Chat\Tests\Models\User;
use RonasIT\Chat\Tests\Support\ModelTestState;
use RonasIT\Chat\Tests\Support\TableTestState;

class ConversationTest extends TestCase
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

        ChatRouter::$isBlockedBaseRoutes = false;
    }

    public function testGetPrivate()
    {
        $response = $this->actingAs(self::$groupCreator)->json('get', '/conversations/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_private', $response->json());
    }

    public function testGetGroup()
    {
        $response = $this->actingAs(self::$groupCreator)->json('get', '/conversations/6');

        $response->assertOk();

        $this->assertEqualsFixture('get_group', $response->json());
    }

    public function testGetWithRelations()
    {
        $response = $this->actingAs(self::$groupCreator)->json(
            method: 'get',
            uri: '/conversations/6',
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
        $response = $this->actingAs(self::$someAuthUser)->json('get', '/conversations/6');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs(self::$groupCreator)->json('get', '/conversations/0');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);
    }

    public function testGetNoAuth()
    {
        $response = $this->json('get', '/conversations/1');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testDeletePrivate(): void
    {
        $response = $this->actingAs(self::$groupCreator)->json('delete', '/conversations/1');

        $response->assertNoContent();

        Notification::assertSentTo(self::$groupCreator, ConversationDeletedNotification::class);
        Notification::assertSentTo(self::$user, ConversationDeletedNotification::class);

        self::$conversationState->assertChangesEqualsFixture('private_deleted');
    }

    public function testDeleteGroup(): void
    {
        $response = $this->actingAs(self::$groupCreator)->json('delete', '/conversations/6');

        $response->assertNoContent();

        Notification::assertSentTo(self::$groupCreator, ConversationDeletedNotification::class);
        Notification::assertSentTo(self::$user, ConversationDeletedNotification::class);

        self::$conversationState->assertChangesEqualsFixture('group_deleted');
    }

    public function testDeleteGroupNonCreator()
    {
        $response = $this->actingAs(self::$user)->json('delete', '/conversations/6');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$conversationState->assertNotChanged();
    }

    public function testDeletePrivateNonMember()
    {
        $response = $this->actingAs(self::$someAuthUser)->json('delete', '/conversations/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

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
        $response = $this->actingAs(self::$groupCreator)->json('delete', '/conversations/0');

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
        $response = $this->actingAs(self::$groupCreator)->json('get', '/conversations', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testSearchNoAuth()
    {
        $response = $this->json('get', '/conversations');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testGetOrCreatePrivateExists(): void
    {
        $response = $this->actingAs(self::$groupCreator)->json('post', '/conversations/private', ['participant_id' => 2]);

        $response->assertOk();

        $this->assertEqualsFixture('get_or_create_private_exists', $response->json());

        self::$conversationState->assertNotChanged();
        self::$conversationMemberState->assertNotChanged();
    }

    public function testGetOrCreatePrivateCreatesNew(): void
    {
        $response = $this->actingAs(self::$groupCreator)->json('post', '/conversations/private', ['participant_id' => 4]);

        $response->assertOk();

        $this->assertEqualsFixture('get_or_create_private_created_response', $response->json());

        self::$conversationState->assertChangesEqualsFixture('get_or_create_private_created');
        self::$conversationMemberState->assertChangesEqualsFixture('get_or_create_private_created');
    }

    public function testGetOrCreatePrivateSelf(): void
    {
        $response = $this->actingAs(self::$groupCreator)->json('post', '/conversations/private', ['participant_id' => 1]);

        $response->assertUnprocessable();

        $response->assertJson(['message' => 'You cannot start a conversation with yourself.']);

        self::$conversationState->assertNotChanged();
    }

    public function testGetOrCreatePrivateNoAuth(): void
    {
        $response = $this->json('post', '/conversations/private', ['participant_id' => 2]);

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);

        self::$conversationState->assertNotChanged();
    }

    public function testUpdate(): void
    {
        $response = $this->actingAs(self::$groupCreator)->json('put', '/conversations/6', [
            'title' => 'New Title',
            'cover_id' => 2,
            'member_ids' => [1, 3, 4],
        ]);

        $response->assertNoContent();

        self::$conversationState->assertChangesEqualsFixture('updated');
        self::$conversationMemberState->assertChangesEqualsFixture('updated');
    }

    public function testUpdateCheckValidation(): void
    {
        $response = $this->actingAs(self::$groupCreator)->json('put', '/conversations/6', [
            'member_ids' => [1, 3, 4, 0],
        ]);

        $response->assertUnprocessable();

        $response->assertJson(['message' => 'The selected member ids is invalid.']);

        self::$conversationState->assertNotChanged();
        self::$conversationMemberState->assertNotChanged();
    }

    public function testUpdateByNonCreator(): void
    {
        $response = $this->actingAs(self::$user)->json('put', '/conversations/6');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$conversationState->assertNotChanged();
        self::$conversationMemberState->assertNotChanged();
    }

    public function testUpdateNotFound(): void
    {
        $response = $this->actingAs(self::$groupCreator)->json('put', '/conversations/0');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);

        self::$conversationState->assertNotChanged();
        self::$conversationMemberState->assertNotChanged();
    }

    public function testUpdateNoAuth(): void
    {
        $response = $this->json('put', '/conversations/6');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);

        self::$conversationState->assertNotChanged();
        self::$conversationMemberState->assertNotChanged();
    }
}
