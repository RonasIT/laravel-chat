<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use RonasIT\Chat\Enums\ChatRouteActionEnum;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Models\Message;
use RonasIT\Chat\Notifications\NewMessageNotification;
use RonasIT\Chat\Tests\Models\User;
use RonasIT\Chat\Tests\Support\ModelTestState;
use RonasIT\Chat\Tests\Support\TableTestState;

class MessageStaticTest extends TestCase
{
    protected static User $firstUser;
    protected static User $secondUser;
    protected static User $someAuthUser;

    protected static ModelTestState $conversationState;
    protected static ModelTestState $messageState;
    protected static TableTestState $conversationMemberState;

    public function setUp(): void
    {
        parent::setUp();

        self::$firstUser ??= User::find(1);
        self::$secondUser ??= User::find(2);
        self::$someAuthUser ??= User::find(3);

        self::$conversationState = new ModelTestState(Conversation::class);
        self::$messageState = new ModelTestState(Message::class);
        self::$conversationMemberState = new TableTestState('conversation_member');
    }

    public function testEverythingDisabledExceptSearch(): void
    {
        Route::chat(ChatRouteActionEnum::MessageSearch);

        $responseSearch = $this->actingAs(self::$firstUser)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$firstUser)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$firstUser)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$firstUser)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$firstUser)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$firstUser)->postJson('/messages');

        $responseSearchMessages->assertOk();

        $responseGet->assertNotFound();
        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearch->assertNotFound();
        $responseCreate->assertNotFound();
    }

    public function testEverythingDisabledExceptCreate(): void
    {
        Route::chat(ChatRouteActionEnum::MessageCreate);

        $data = $this->getJsonFixture('create_message_request');

        $responseSearch = $this->actingAs(self::$firstUser)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$firstUser)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$firstUser)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$firstUser)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$firstUser)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$firstUser)->postJson('/messages', $data);

        $responseCreate->assertOk();

        $responseGet->assertNotFound();
        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearch->assertNotFound();
        $responseSearchMessages->assertNotFound();
    }

    public function testCreateInExistsConversation(): void
    {
        Notification::fake();

        Route::chat(ChatRouteActionEnum::MessageCreate);

        $data = $this->getJsonFixture('create_message_request');

        $response = $this->actingAs(self::$firstUser)->json('post', '/messages', $data);

        Notification::assertSentTo(self::$secondUser, NewMessageNotification::class);

        $response->assertOk();

        $this->assertEqualsFixture('create_message_response', $response->json());

        self::$conversationState->assertNotChanged();
        self::$messageState->assertChangesEqualsFixture('created');
        self::$conversationMemberState->assertNotChanged();
    }

    public function testCreateInNotExistsConversation(): void
    {
        Notification::fake();

        Route::chat(ChatRouteActionEnum::MessageCreate);

        $data = $this->getJsonFixture('create_message_in_exists_conversation_request');

        $response = $this->actingAs(self::$secondUser)->json('post', '/messages', $data);

        $response->assertOk();

        Notification::assertSentTo(User::find(5), NewMessageNotification::class);

        $this->assertEqualsFixture('create_message_in_exists_conversation_response', $response->json());

        self::$conversationState->assertChangesEqualsFixture('created');
        self::$messageState->assertChangesEqualsFixture('created_with_new_conversation');
        self::$conversationMemberState->assertChangesEqualsFixture('created');
    }

    public function testCreateSelfMessage(): void
    {
        Route::chat(ChatRouteActionEnum::MessageCreate);

        $data = $this->getJsonFixture('create_message_request');

        $response = $this->actingAs(self::$secondUser)->json('post', '/messages', $data);

        $response->assertBadRequest();

        $response->assertJson(['message' => 'You cannot send a message to yourself.']);

        self::$conversationState->assertNotChanged();

        self::$messageState->assertNotChanged();
    }

    public function testCreateWithAttachment(): void
    {
        Notification::fake();

        Route::chat(ChatRouteActionEnum::MessageCreate);

        $data = $this->getJsonFixture('create_message_with_attachment_request');

        $response = $this->actingAs(self::$firstUser)->json('post', '/messages', $data);

        Notification::assertSentTo(self::$secondUser, NewMessageNotification::class);

        $response->assertOk();

        $this->assertEqualsFixture('create_message_with_attachment_response', $response->json());

        self::$conversationState->assertNotChanged();

        self::$messageState->assertChangesEqualsFixture('created_with_attachment');
    }

    public function testCreateWithConversationId(): void
    {
        Notification::fake();

        Route::chat(ChatRouteActionEnum::MessageCreate);

        $data = $this->getJsonFixture('create_message_with_conversation_id_request');

        $response = $this->actingAs(self::$firstUser)->json('post', '/messages', $data);

        Notification::assertSentTo(self::$secondUser, NewMessageNotification::class);

        $response->assertOk();

        $this->assertEqualsFixture('create_message_with_conversation_id_response', $response->json());

        self::$conversationState->assertNotChanged();
        self::$messageState->assertChangesEqualsFixture('created_with_conversation_id');
        self::$conversationMemberState->assertNotChanged();
    }

    public function testCreateAsNonMember(): void
    {
        Route::chat(ChatRouteActionEnum::MessageCreate);

        $data = $this->getJsonFixture('create_message_with_conversation_id_request');

        $response = $this->actingAs(self::$someAuthUser)->json('post', '/messages', $data);

        $response->assertForbidden();

        $response->assertJson(['message' => 'You are not a member of this conversation.']);

        self::$conversationState->assertNotChanged();

        self::$messageState->assertNotChanged();
    }

    public function testCreateEndpointDisabled(): void
    {
        $response = $this->actingAs(self::$secondUser)->json('post', '/messages');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);

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
        Route::chat(ChatRouteActionEnum::MessageSearch);

        $response = $this->actingAs(self::$firstUser)->json('get', '/messages', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testSearchEndpointDisabled()
    {
        $response = $this->actingAs(self::$firstUser)->json('get', '/messages');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);
    }
}
