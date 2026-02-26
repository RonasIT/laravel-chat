<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use RonasIT\Chat\Enums\ChatRouteActionEnum;
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
    }

    public function testEverythingDisabledExceptSearch(): void
    {
        Route::chat(ChatRouteActionEnum::MessageSearch);

        $responseSearch = $this->actingAs(self::$firstUser)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$firstUser)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$firstUser)->deleteJson('/conversations/1');
        $responseGetOrCreatePrivate = $this->actingAs(self::$firstUser)->postJson('/conversations/private');
        $responseSearchMessages = $this->actingAs(self::$firstUser)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$firstUser)->postJson('/messages/1');
        $responseRead = $this->actingAs(self::$firstUser)->putJson('messages/1/read');

        $responseSearchMessages->assertOk();

        $responseGet->assertNotFound();
        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetOrCreatePrivate->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testEverythingDisabledExceptCreate(): void
    {
        Route::chat(ChatRouteActionEnum::MessageCreate);

        $data = $this->getJsonFixture('create_request');

        $responseSearch = $this->actingAs(self::$firstUser)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$firstUser)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$firstUser)->deleteJson('/conversations/1');
        $responseGetOrCreatePrivate = $this->actingAs(self::$firstUser)->postJson('/conversations/private');
        $responseSearchMessages = $this->actingAs(self::$firstUser)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$firstUser)->postJson('/messages/1', $data);
        $responseRead = $this->actingAs(self::$firstUser)->putJson('messages/1/read');

        $responseCreate->assertOk();

        $responseGet->assertNotFound();
        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetOrCreatePrivate->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testEverythingDisabledExceptRead(): void
    {
        Route::chat(ChatRouteActionEnum::MessageRead);

        $responseSearch = $this->actingAs(self::$firstUser)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$firstUser)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$firstUser)->deleteJson('/conversations/1');
        $responseGetOrCreatePrivate = $this->actingAs(self::$firstUser)->postJson('/conversations/private');
        $responseSearchMessages = $this->actingAs(self::$firstUser)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$firstUser)->postJson('/messages/1');
        $responseRead = $this->actingAs(self::$secondUser)->putJson('messages/1/read');

        $responseRead->assertNoContent();

        $responseGet->assertNotFound();
        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetOrCreatePrivate->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
    }

    public function testCreate(): void
    {
        Route::chat(ChatRouteActionEnum::MessageCreate);

        $response = $this->actingAs(self::$firstUser)->json('post', '/messages/1', ['text' => 'hello']);

        Notification::assertSentTo(self::$secondUser, NewMessageNotification::class);

        $response->assertOk();

        $this->assertEqualsFixture('create_response', $response->json());

        self::$messageState->assertChangesEqualsFixture('created');
    }

    public function testCreateConversationNotExists(): void
    {
        Route::chat(ChatRouteActionEnum::MessageCreate);

        $response = $this->actingAs(self::$firstUser)->json('post', '/messages/0', ['text' => 'hello']);

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);

        self::$messageState->assertNotChanged();

        Notification::assertNothingSent();
    }

    public function testCreateNonMember(): void
    {
        Route::chat(ChatRouteActionEnum::MessageCreate);

        $response = $this->actingAs(self::$someAuthUser)->json('post', '/messages/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$messageState->assertNotChanged();
    }

    public function testCreateWithAttachment(): void
    {
        Route::chat(ChatRouteActionEnum::MessageCreate);

        $data = $this->getJsonFixture('create_with_attachment_request');

        $response = $this->actingAs(self::$firstUser)->json('post', '/messages/1', $data);

        Notification::assertSentTo(self::$secondUser, NewMessageNotification::class);

        $response->assertOk();

        $this->assertEqualsFixture('create_with_attachment_response', $response->json());

        self::$messageState->assertChangesEqualsFixture('created_with_attachment');
    }

    public function testCreateEndpointDisabled(): void
    {
        $response = $this->actingAs(self::$secondUser)->json('post', '/messages/1');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);

        self::$messageState->assertNotChanged();
    }

    public function testRead(): void
    {
        Route::chat(ChatRouteActionEnum::MessageRead);

        $response = $this->actingAs(self::$firstUser)->json('put', '/messages/3/read');

        $response->assertNoContent();

        self::$conversationMemberState->assertChangesEqualsFixture('read');
        self::$messageState->assertNotChanged();
    }

    public function testReadNonMember(): void
    {
        Route::chat(ChatRouteActionEnum::MessageRead);

        $response = $this->actingAs(self::$someAuthUser)->json('put', '/messages/1/read');

        $response->assertForbidden();

        $response->assertJson(['message' => 'This action is unauthorized.']);

        self::$messageState->assertNotChanged();
    }

    public function testNotExistsRead(): void
    {
        Route::chat(ChatRouteActionEnum::MessageRead);

        $response = $this->actingAs(self::$secondUser)->json('put', '/messages/0/read');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Message does not exist']);

        self::$messageState->assertNotChanged();
    }

    public function testReadEndpointDisabled(): void
    {
        $response = $this->actingAs(self::$firstUser)->json('put', '/messages/1/read');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);

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
    public function testSearch(array $filter, string $fixture): void
    {
        Route::chat(ChatRouteActionEnum::MessageSearch);

        $response = $this->actingAs(self::$firstUser)->json('get', '/messages', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testSearchEndpointDisabled(): void
    {
        $response = $this->actingAs(self::$firstUser)->json('get', '/messages');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);
    }
}
