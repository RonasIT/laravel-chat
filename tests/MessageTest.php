<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use RonasIT\Chat\ChatRouter;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Models\Message;
use RonasIT\Chat\Models\ReadMessage;
use RonasIT\Chat\Tests\Models\User;
use RonasIT\Chat\Tests\Support\ModelTestState;
use RonasIT\Chat\Tests\Support\TableTestState;

class MessageTest extends TestCase
{
    protected static User $firstUser;
    protected static User $secondUser;
    protected static User $someAuthUser;

    protected static ModelTestState $conversationState;
    protected static ModelTestState $messageState;
    protected static TableTestState $conversationMemberState;
    protected static ModelTestState $readMessageState;
    protected static TableTestState $pinnedMessageState;

    public function setUp(): void
    {
        parent::setUp();

        self::$firstUser ??= User::find(1);
        self::$secondUser ??= User::find(2);
        self::$someAuthUser ??= User::find(3);

        self::$conversationState = new ModelTestState(Conversation::class);
        self::$messageState = new ModelTestState(Message::class);
        self::$conversationMemberState = new TableTestState('conversation_member');
        self::$readMessageState = new ModelTestState(ReadMessage::class);
        self::$pinnedMessageState = new TableTestState('pinned_messages');

        ChatRouter::$isBlockedBaseRoutes = false;
    }

    public function testCreateInExistsConversation(): void
    {
        $data = $this->getJsonFixture('create_message_request');

        $response = $this->actingAs(self::$firstUser)->json('post', '/messages', $data);

        $this->assertBroadcastNotificationSent('create_in_exists_conversation');

        $response->assertCreated();

        $this->assertEqualsFixture('create_message_response', $response->json());

        self::$conversationState->assertChangesEqualsFixture('created');
        self::$messageState->assertChangesEqualsFixture('created');
        self::$conversationMemberState->assertNotChanged();
    }

    public function testCreateInNotExistsConversation(): void
    {
        $data = $this->getJsonFixture('create_message_in_exists_conversation_request');

        $response = $this->actingAs(self::$secondUser)->json('post', '/messages', $data);

        $response->assertCreated();

        $this->assertBroadcastNotificationSent('create_in_not_exists_conversation');

        $this->assertEqualsFixture('create_message_in_exists_conversation_response', $response->json());

        self::$conversationState->assertChangesEqualsFixture('created_with_new_conversation');
        self::$messageState->assertChangesEqualsFixture('created_with_new_conversation');
        self::$conversationMemberState->assertChangesEqualsFixture('created');
    }

    public function testCreateSelfMessage(): void
    {
        $data = $this->getJsonFixture('create_message_request');

        $response = $this->actingAs(self::$secondUser)->json('post', '/messages', $data);

        $response->assertUnprocessable();

        $response->assertJson(['message' => 'The recipient id must not be the same as the message sender id.']);

        self::$conversationState->assertNotChanged();

        self::$messageState->assertNotChanged();
    }

    public function testCreateWithAttachment(): void
    {
        $data = $this->getJsonFixture('create_message_with_attachment_request');

        $response = $this->actingAs(self::$firstUser)->json('post', '/messages', $data);

        $this->assertBroadcastNotificationSent('create_with_attachment');

        $response->assertCreated();

        $this->assertEqualsFixture('create_message_with_attachment_response', $response->json());

        self::$conversationState->assertChangesEqualsFixture('created_with_attachment');

        self::$messageState->assertChangesEqualsFixture('created_with_attachment');
    }

    public function testCreateWithConversationId(): void
    {
        $data = $this->getJsonFixture('create_message_with_conversation_id_request');

        $response = $this->actingAs(self::$firstUser)->json('post', '/messages', $data);

        $this->assertBroadcastNotificationSent('create_with_conversation_id');

        $response->assertCreated();

        $this->assertEqualsFixture('create_message_with_conversation_id_response', $response->json());

        self::$conversationState->assertChangesEqualsFixture('created_with_conversation_id');

        self::$messageState->assertChangesEqualsFixture('created_with_conversation_id');
        self::$conversationMemberState->assertNotChanged();
    }

    public function testCreateAsNonMember(): void
    {
        $data = $this->getJsonFixture('create_message_with_conversation_id_request');

        $response = $this->actingAs(self::$someAuthUser)->json('post', '/messages', $data);

        $response->assertUnprocessable();

        $response->assertJson(['message' => 'The selected conversation id is invalid.']);

        self::$conversationState->assertNotChanged();

        self::$messageState->assertNotChanged();
    }

    public function testCreateConversationNotExists(): void
    {
        $response = $this->actingAs(self::$someAuthUser)->json('post', '/messages', [
            'conversation_id' => 0,
            'text' => 'test',
        ]);

        $response->assertUnprocessable();

        $response->assertJson(['message' => 'The selected conversation id is invalid.']);

        self::$conversationState->assertNotChanged();

        self::$messageState->assertNotChanged();
    }

    public function testCreateNoAuth(): void
    {
        $response = $this->postJson('/messages');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);

        self::$conversationState->assertNotChanged();

        self::$messageState->assertNotChanged();
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

    public function testRead(): void
    {
        $response = $this->actingAs(self::$secondUser)->postJson('/messages/7/read-to');

        $response->assertNoContent();

        self::$readMessageState->assertChangesEqualsFixture('read');

        $this->assertBroadcastNotificationSent('read');
    }

    public function testReadAlreadyRead(): void
    {
        $response = $this->actingAs(self::$someAuthUser)->postJson('/messages/2/read-to');

        $response->assertNoContent();

        self::$readMessageState->assertNotChanged();

        Notification::assertNothingSent();
    }

    public function testReadAsNonMember(): void
    {
        $response = $this->actingAs(self::$someAuthUser)->postJson('/messages/1/read-to');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$readMessageState->assertNotChanged();
    }

    public function testReadNoAuth(): void
    {
        $response = $this->postJson('/messages/1/read-to');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);

        self::$readMessageState->assertNotChanged();
    }

    public function testReadUpToWithInvalidId(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/abc/read-to');

        $response->assertNotFound();
    }

    public function testPin(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/2/pin');

        $response->assertNoContent();

        self::$pinnedMessageState->assertChangesEqualsFixture('pinned');

        $this->assertBroadcastNotificationSent('pin');
    }

    public function testPinAlreadyPinned(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/1/pin');

        $response->assertNoContent();

        self::$pinnedMessageState->assertNotChanged();

        Notification::assertNothingSent();
    }

    public function testPinAsNonMember(): void
    {
        $response = $this->actingAs(self::$someAuthUser)->postJson('/messages/1/pin');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$pinnedMessageState->assertNotChanged();
    }

    public function testPinNotFound(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/0/pin');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Message does not exist']);

        self::$pinnedMessageState->assertNotChanged();
    }

    public function testPinWithInvalidId(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/abc/pin');

        $response->assertNotFound();

        self::$pinnedMessageState->assertNotChanged();
    }

    public function testPinNoAuth(): void
    {
        $response = $this->postJson('/messages/1/pin');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);

        self::$pinnedMessageState->assertNotChanged();
    }

    public function testUnpin(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/1/unpin');

        $response->assertNoContent();

        self::$pinnedMessageState->assertChangesEqualsFixture('unpinned');

        $this->assertBroadcastNotificationSent('unpin');
    }

    public function testUnpinNotPinned(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/2/unpin');

        $response->assertConflict();

        $response->assertJson(['message' => 'Message is not pinned.']);

        self::$pinnedMessageState->assertNotChanged();

        Notification::assertNothingSent();
    }

    public function testUnpinAsNonMember(): void
    {
        $response = $this->actingAs(self::$someAuthUser)->postJson('/messages/1/unpin');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$pinnedMessageState->assertNotChanged();
    }

    public function testUnpinNotFound(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/0/unpin');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Message does not exist']);

        self::$pinnedMessageState->assertNotChanged();
    }

    public function testUnpinWithInvalidId(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/abc/unpin');

        $response->assertNotFound();

        self::$pinnedMessageState->assertNotChanged();
    }

    public function testUnpinNoAuth(): void
    {
        $response = $this->postJson('/messages/1/unpin');

        $response->assertUnauthorized();

        $response->assertJson(['message' => 'Unauthenticated.']);

        self::$pinnedMessageState->assertNotChanged();
    }
}
