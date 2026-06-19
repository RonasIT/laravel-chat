<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use RonasIT\Chat\Enums\ChatRouteActionEnum;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Models\Message;
use RonasIT\Chat\Models\ReadMessage;
use RonasIT\Chat\Tests\Models\User;
use RonasIT\Chat\Tests\Support\Attributes\RegisterChatRoutes;
use RonasIT\Chat\Tests\Support\ModelTestState;
use RonasIT\Chat\Tests\Support\TableTestState;
use RonasIT\Chat\Tests\Support\Traits\RegistersChatRoutesTrait;

class MessageStaticTest extends TestCase
{
    use RegistersChatRoutesTrait;

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
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessagesSearch)]
    public function testEverythingDisabledExceptSearch(): void
    {
        $responseSearch = $this->actingAs(self::$firstUser)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$firstUser)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$firstUser)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$firstUser)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$firstUser)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$firstUser)->postJson('/messages');
        $responseRead = $this->actingAs(self::$firstUser)->postJson('/messages/1/read-to');

        $responseSearchMessages->assertOk();

        $responseCreate->assertMethodNotAllowed();

        $responseGet->assertNotFound();
        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseRead->assertNotFound();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageCreate)]
    public function testEverythingDisabledExceptCreate(): void
    {
        $data = $this->getJsonFixture('create_message_request');

        $responseSearch = $this->actingAs(self::$firstUser)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$firstUser)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$firstUser)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$firstUser)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$firstUser)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$firstUser)->postJson('/messages', $data);
        $responseRead = $this->actingAs(self::$firstUser)->postJson('/messages/1/read-to');

        $responseCreate->assertCreated();

        $responseSearchMessages->assertMethodNotAllowed();

        $responseGet->assertNotFound();
        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseRead->assertNotFound();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageCreate)]
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

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageCreate)]
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

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageCreate)]
    public function testCreateSelfMessage(): void
    {
        $data = $this->getJsonFixture('create_message_request');

        $response = $this->actingAs(self::$secondUser)->json('post', '/messages', $data);

        $response->assertUnprocessable();

        $response->assertJson(['message' => 'The recipient id must not be the same as the message sender id.']);

        self::$conversationState->assertNotChanged();

        self::$messageState->assertNotChanged();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageCreate)]
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

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageCreate)]
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

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageCreate)]
    public function testCreateAsNonMember(): void
    {
        $data = $this->getJsonFixture('create_message_with_conversation_id_request');

        $response = $this->actingAs(self::$someAuthUser)->json('post', '/messages', $data);

        $response->assertUnprocessable();

        $response->assertJson(['message' => 'The selected conversation id is invalid.']);

        self::$conversationState->assertNotChanged();

        self::$messageState->assertNotChanged();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageCreate)]
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

    #[RegisterChatRoutes(ChatRouteActionEnum::ConversationsSearch)]
    public function testCreateEndpointDisabled(): void
    {
        $response = $this->actingAs(self::$secondUser)->json('post', '/messages');

        $response->assertNotFound();

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
                    'with_conversation_identity' => true,
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
            [
                'filter' => [
                    'order_by' => 'created_at',
                    'desc' => true,
                ],
                'fixture' => 'search_order_by_created_at',
            ],
        ];
    }

    #[DataProvider('getSearchFilters')]
    #[RegisterChatRoutes(ChatRouteActionEnum::MessagesSearch)]
    public function testSearch(array $filter, string $fixture)
    {
        $response = $this->actingAs(self::$firstUser)->json('get', '/messages', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessagesSearch)]
    public function testSearchWithInvalidOrderBy()
    {
        $response = $this->actingAs(self::$firstUser)->json('get', '/messages', [
            'order_by' => 'invalid_field',
        ]);

        $response->assertUnprocessable();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::ConversationsSearch)]
    public function testSearchEndpointDisabled()
    {
        $response = $this->actingAs(self::$firstUser)->json('get', '/messages');

        $response->assertNotFound();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessagesRead)]
    public function testEverythingDisabledExceptRead(): void
    {
        $responseSearch = $this->actingAs(self::$firstUser)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$firstUser)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$firstUser)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$firstUser)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$firstUser)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$firstUser)->postJson('/messages');
        $responseRead = $this->actingAs(self::$secondUser)->postJson('/messages/1/read-to');

        $responseRead->assertNoContent();

        $responseSearch->assertNotFound();
        $responseGet->assertNotFound();
        $responseDelete->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessagesRead)]
    public function testRead(): void
    {
        $response = $this->actingAs(self::$secondUser)->postJson('/messages/7/read-to');

        $response->assertNoContent();

        self::$readMessageState->assertChangesEqualsFixture('read');

        $this->assertBroadcastNotificationSent('read');
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessagesRead)]
    public function testReadAlreadyRead(): void
    {
        $response = $this->actingAs(self::$someAuthUser)->postJson('/messages/2/read-to');

        $response->assertNoContent();

        self::$readMessageState->assertNotChanged();

        Notification::assertNothingSent();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessagesRead)]
    public function testReadAsNonMember(): void
    {
        $response = $this->actingAs(self::$someAuthUser)->postJson('/messages/1/read-to');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$readMessageState->assertNotChanged();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessagesRead)]
    public function testReadUpToWithInvalidId(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/abc/read-to');

        $response->assertNotFound();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::ConversationsSearch)]
    public function testReadEndpointDisabled(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/1/read-to');

        $response->assertNotFound();

        self::$readMessageState->assertNotChanged();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessagePin)]
    public function testEverythingDisabledExceptPin(): void
    {
        $responseSearch = $this->actingAs(self::$firstUser)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$firstUser)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$firstUser)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$firstUser)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$firstUser)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$firstUser)->postJson('/messages');
        $responseRead = $this->actingAs(self::$firstUser)->postJson('/messages/1/read-to');
        $responsePin = $this->actingAs(self::$firstUser)->postJson('/messages/1/pin');
        $responseUnpin = $this->actingAs(self::$firstUser)->postJson('/messages/1/unpin');

        $responsePin->assertNoContent();

        $responseSearch->assertNotFound();
        $responseGet->assertNotFound();
        $responseDelete->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
        $responseUnpin->assertNotFound();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageUnpin)]
    public function testEverythingDisabledExceptUnpin(): void
    {
        $responseSearch = $this->actingAs(self::$firstUser)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$firstUser)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$firstUser)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$firstUser)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$firstUser)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$firstUser)->postJson('/messages');
        $responseRead = $this->actingAs(self::$firstUser)->postJson('/messages/1/read-to');
        $responsePin = $this->actingAs(self::$firstUser)->postJson('/messages/1/pin');
        $responseUnpin = $this->actingAs(self::$firstUser)->postJson('/messages/1/unpin');

        $responseUnpin->assertNoContent();

        $responseSearch->assertNotFound();
        $responseGet->assertNotFound();
        $responseDelete->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
        $responsePin->assertNotFound();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessagePin)]
    public function testPin(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/2/pin');

        $response->assertNoContent();

        self::$pinnedMessageState->assertChangesEqualsFixture('pinned');

        $this->assertBroadcastNotificationSent('pin');
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessagePin)]
    public function testPinAlreadyPinned(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/1/pin');

        $response->assertNoContent();

        self::$pinnedMessageState->assertNotChanged();

        Notification::assertNothingSent();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessagePin)]
    public function testPinAsNonMember(): void
    {
        $response = $this->actingAs(self::$someAuthUser)->postJson('/messages/1/pin');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$pinnedMessageState->assertNotChanged();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessagePin)]
    public function testPinNotFound(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/0/pin');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Message does not exist']);

        self::$pinnedMessageState->assertNotChanged();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessagePin)]
    public function testPinWithInvalidId(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/abc/pin');

        $response->assertNotFound();

        self::$pinnedMessageState->assertNotChanged();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::ConversationsSearch)]
    public function testPinEndpointDisabled(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/1/pin');

        $response->assertNotFound();

        self::$pinnedMessageState->assertNotChanged();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageUnpin)]
    public function testUnpin(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/1/unpin');

        $response->assertNoContent();

        self::$pinnedMessageState->assertChangesEqualsFixture('unpinned');

        $this->assertBroadcastNotificationSent('unpin');
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageUnpin)]
    public function testUnpinNotPinned(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/2/unpin');

        $response->assertConflict();

        $response->assertJson(['message' => 'Message is not pinned.']);

        self::$pinnedMessageState->assertNotChanged();

        Notification::assertNothingSent();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageUnpin)]
    public function testUnpinAsNonMember(): void
    {
        $response = $this->actingAs(self::$someAuthUser)->postJson('/messages/1/unpin');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$pinnedMessageState->assertNotChanged();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageUnpin)]
    public function testUnpinAsNonMemberNotPinned(): void
    {
        $response = $this->actingAs(self::$someAuthUser)->postJson('/messages/3/unpin');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$pinnedMessageState->assertNotChanged();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageUnpin)]
    public function testUnpinNotFound(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/0/unpin');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Message does not exist']);

        self::$pinnedMessageState->assertNotChanged();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::MessageUnpin)]
    public function testUnpinWithInvalidId(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/abc/unpin');

        $response->assertNotFound();

        self::$pinnedMessageState->assertNotChanged();
    }

    #[RegisterChatRoutes(ChatRouteActionEnum::ConversationsSearch)]
    public function testUnpinEndpointDisabled(): void
    {
        $response = $this->actingAs(self::$firstUser)->postJson('/messages/1/unpin');

        $response->assertNotFound();

        self::$pinnedMessageState->assertNotChanged();
    }
}
