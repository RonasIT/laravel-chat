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
    protected static ModelTestState $messageTestState;

    public function setUp(): void
    {
        parent::setUp();

        self::$firstUser ??= User::find(1);
        self::$secondUser ??= User::find(2);
        self::$someAuthUser ??= User::find(3);

        self::$conversationTestState = new ModelTestState(Conversation::class);
        self::$messageTestState = new ModelTestState(Message::class);
    }

    public function testCreateInExistsConversation(): void
    {
        $data = $this->getJsonFixture('create_message_request.json');

        $response = $this->actingAs(self::$firstUser)->json('POST', '/messages', $data);

        Notification::fake();

        self::$secondUser->notify(new NewMessageNotification());

        Notification::assertSentTo(self::$secondUser, NewMessageNotification::class);

        $response->assertOk();

        $this->assertEqualsFixture('create_message_response.json', $response->json());

        self::$conversationTestState->assertNotChanged();

        self::$messageTestState->assertChangesEqualsFixture('message_created_messages_state.json');
    }

    public function testCreateInNotExistsConversation(): void
    {
        $data = $this->getJsonFixture('create_message_in_exists_conversation_request.json');

        $response = $this->actingAs(self::$secondUser)->json('POST', '/messages', $data);

        $response->assertOk();

        Notification::fake();

        self::$secondUser->notify(new NewMessageNotification());

        Notification::assertSentTo(self::$secondUser, NewMessageNotification::class);

        $this->assertEqualsFixture('create_message_in_exists_conversation_response.json', $response->json());

        self::$conversationTestState->assertChangesEqualsFixture('conversation_created_messages_state.json');

        self::$messageTestState->assertChangesEqualsFixture('messages_created_messages_with_new_conversation_state.json');
    }

    public function testCreateSelfMessage(): void
    {
        $data = $this->getJsonFixture('create_message_request.json');

        $response = $this->actingAs(self::$secondUser)->json('POST', '/messages', $data);

        $response->assertBadRequest();

        $response->assertJson(['message' => 'You cannot send a message to yourself.']);

        self::$conversationTestState->assertNotChanged();

        self::$messageTestState->assertNotChanged();
    }

    public function testRead()
    {
        $response = $this->actingAs(self::$secondUser)->json('put', '/messages/1/read');

        $response->assertNoContent();

        self::$messageTestState->assertChangesEqualsFixture('message_read_messages_state.json');
    }

    public function testNotActingRecipientRead()
    {
        $response = $this->actingAs(self::$firstUser)->json('put', '/messages/1/read');

        $response->assertStatus(403);

        $response->assertJson(['message' => 'You are not the recipient of this message.']);

        self::$messageTestState->assertNotChanged();
    }

    public function testNotExistsRead()
    {
        $response = $this->actingAs(self::$secondUser)->json('put', '/messages/0/read');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Message does not exist']);

        self::$messageTestState->assertNotChanged();
    }

    public static function getSearchFilters(): array
    {
        return [
            [
                'filter' => ['all' => true],
                'fixture' => 'search_all.json',
            ],
        ];
    }

    #[DataProvider('getSearchFilters')]
    public function testSearch($filter,$fixture)
    {
        $response = $this->actingAs(self::$firstUser)->json('get', '/messages', $filter);

        $response->assertOk();

        $this->assertEqualsFixture($fixture, $response->json());
    }
}