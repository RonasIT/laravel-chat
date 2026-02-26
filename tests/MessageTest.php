<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use RonasIT\Chat\ChatRouter;
use RonasIT\Chat\Models\Message;
use RonasIT\Chat\Notifications\NewMessageNotification;
use RonasIT\Chat\Tests\Models\User;
use RonasIT\Chat\Tests\Support\ModelTestState;
use RonasIT\Chat\Tests\Support\TableTestState;

class MessageTest extends TestCase
{
    protected static User $firstUser;
    protected static User $secondUser;
    protected static User $someAuthUser;

    protected static ModelTestState $messageState;
    protected static TableTestState $conversationMemberState;

    public function setUp(): void
    {
        parent::setUp();

        self::$firstUser ??= User::find(1);
        self::$secondUser ??= User::find(2);
        self::$someAuthUser ??= User::find(3);

        self::$messageState = new ModelTestState(Message::class);
        self::$conversationMemberState = new TableTestState('conversation_member');

        ChatRouter::$isBlockedBaseRoutes = false;
    }

    public function testCreate(): void
    {
        $response = $this->actingAs(self::$firstUser)->json('post', '/messages/1', ['text' => 'hello']);

        Notification::assertSentTo(self::$secondUser, NewMessageNotification::class);

        $response->assertOk();

        $this->assertEqualsFixture('create_response', $response->json());

        self::$messageState->assertChangesEqualsFixture('created');
    }

    public function testCreateConversationNotExists(): void
    {
        $response = $this->actingAs(self::$firstUser)->json('post', '/messages/0', ['text' => 'hello']);

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);

        self::$messageState->assertNotChanged();

        Notification::assertNothingSent();
    }

    public function testCreateNonMember(): void
    {
        $response = $this->actingAs(self::$someAuthUser)->json('post', '/messages/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$messageState->assertNotChanged();
    }

    public function testCreateWithAttachment(): void
    {
        $data = $this->getJsonFixture('create_with_attachment_request');

        $response = $this->actingAs(self::$firstUser)->json('post', '/messages/1', $data);

        Notification::assertSentTo(self::$secondUser, NewMessageNotification::class);

        $response->assertOk();

        $this->assertEqualsFixture('create_with_attachment_response', $response->json());

        self::$messageState->assertChangesEqualsFixture('created_with_attachment');
    }

    public function testCreateNoAuth(): void
    {
        $response = $this->postJson('/messages/1');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);

        self::$messageState->assertNotChanged();
    }

    public function testRead(): void
    {
        $response = $this->actingAs(self::$firstUser)->json('put', '/messages/3/read');

        $response->assertNoContent();

        self::$conversationMemberState->assertChangesEqualsFixture('read');
        self::$messageState->assertNotChanged();
    }

    public function testReadNonMember(): void
    {
        $response = $this->actingAs(self::$someAuthUser)->json('put', '/messages/1/read');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$messageState->assertNotChanged();
    }

    public function testNotExistsRead(): void
    {
        $response = $this->actingAs(self::$secondUser)->json('put', '/messages/0/read');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Message does not exist']);

        self::$messageState->assertNotChanged();
    }

    public function testReadNoAuth(): void
    {
        $response = $this->putJson('/messages/3/read');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
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
                        'conversation',
                        'sender',
                        'attachment',
                    ],
                ],
                'fixture' => 'search_with_relations',
            ],
            [
                'filter' => ['conversation_id' => 1],
                'fixture' => 'search_by_conversation_id',
            ],
            [
                'filter' => [
                    'page' => 2,
                    'per_page' => 2,
                ],
                'fixture' => 'search_by_page_per_page',
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
    public function testSearch(array $filter, string $fixture): void
    {
        $response = $this->actingAs(self::$firstUser)->json('get', '/messages', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testSearchNoAuth(): void
    {
        $response = $this->getJson('/messages');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
