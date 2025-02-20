<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Models\Message;
use RonasIT\Chat\Notifications\NewMessageNotification;
use RonasIT\Chat\Tests\Models\User;
use RonasIT\Chat\Tests\Support\ModelTestState;

class MessageTest extends TestCase
{
    protected static User $firstUser;
    protected static User $secondUser;
    protected static User $someAuthUser;

    protected static ModelTestState $conversationTestState;
    protected static ModelTestState $messageState;

    public function setUp(): void
    {
        parent::setUp();

        self::$firstUser ??= User::find(1);
        self::$secondUser ??= User::find(2);
        self::$someAuthUser ??= User::find(3);

        self::$conversationTestState = new ModelTestState(Conversation::class);
        self::$messageState = new ModelTestState(Message::class);
    }

    public function testCreateInExistsConversation(): void
    {
        Notification::fake();

        $data = $this->getJsonFixture('create_message_request');

        $response = $this->actingAs(self::$firstUser)->json('post', '/messages', $data);

        Notification::assertSentTo(self::$secondUser, NewMessageNotification::class);

        $response->assertOk();

        $this->assertEqualsFixture('create_message_response', $response->json());

        self::$conversationTestState->assertNotChanged();

        self::$messageState->assertChangesEqualsFixture('created');
    }

    public function testCreateInNotExistsConversation(): void
    {
        Notification::fake();

        $data = $this->getJsonFixture('create_message_in_exists_conversation_request');

        $response = $this->actingAs(self::$secondUser)->json('post', '/messages', $data);

        $response->assertOk();

        Notification::assertSentTo(User::find(5), NewMessageNotification::class);

        $this->assertEqualsFixture('create_message_in_exists_conversation_response', $response->json());

        self::$conversationTestState->assertChangesEqualsFixture('created');

        self::$messageState->assertChangesEqualsFixture('created_with_new_conversation');
    }

    public function testCreateSelfMessage(): void
    {
        $data = $this->getJsonFixture('create_message_request');

        $response = $this->actingAs(self::$secondUser)->json('post', '/messages', $data);

        $response->assertBadRequest();

        $response->assertJson(['message' => 'You cannot send a message to yourself.']);

        self::$conversationTestState->assertNotChanged();

        self::$messageState->assertNotChanged();
    }

    public function testCreateWithAttachment(): void
    {
        Notification::fake();

        $data = $this->getJsonFixture('create_message_with_attachment_request');

        $response = $this->actingAs(self::$firstUser)->json('post', '/messages', $data);

        Notification::assertSentTo(self::$secondUser, NewMessageNotification::class);

        $response->assertOk();

        $this->assertEqualsFixture('create_message_with_attachment_response', $response->json());

        self::$conversationTestState->assertNotChanged();

        self::$messageState->assertChangesEqualsFixture('created_with_attachment');
    }

    public function testCreateNoAuth(): void
    {
        $response = $this->postJson('/messages');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);

        self::$conversationTestState->assertNotChanged();

        self::$messageState->assertNotChanged();
    }

    public function testRead()
    {
        $response = $this->actingAs(User::find(4))->json('put', '/messages/3/read');

        $response->assertNoContent();

        self::$messageState->assertChangesEqualsFixture('read');
    }

    public function testNotActingRecipientRead()
    {
        $response = $this->actingAs(self::$firstUser)->json('put', '/messages/1/read');

        $response->assertForbidden();

        $response->assertJson(['message' => 'You are not the recipient of this message.']);

        self::$messageState->assertNotChanged();
    }

    public function testNotExistsRead()
    {
        $response = $this->actingAs(self::$secondUser)->json('put', '/messages/0/read');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Message does not exist']);

        self::$messageState->assertNotChanged();
    }

    public function testReadNoAuth()
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
                        'recipient',
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
    public function testSearch(array $filter, string $fixture)
    {
        $response = $this->actingAs(self::$firstUser)->json('get', '/messages', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testSearchNoAuth()
    {
        $response = $this->getJson('/messages');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}