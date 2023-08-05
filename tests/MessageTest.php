<?php

namespace Tests;

use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Notifications\Channels\BroadcastChannel;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;

class MessageTest extends TestCase
{
    protected $user;
    protected $secondUser;

    public function setUp() : void
    {
        parent::setUp();

        $this->user = User::find(1);
        $this->secondUser = User::find(2);
    }

    public function testCreateMessage()
    {
        Notification::fake();

        $response = $this->actingAs($this->user)->json('post', '/messages', [
            'text' => 'New message',
            'recipient_id' => 3
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('created_message_to_fifth_user_response.json', $response->json());

        $this->assertDatabaseHas('messages', [
            'id' => 5,
            'conversation_id' => 3,
            'sender_id' => 1,
            'recipient_id' => 3,
            'text' => 'New message',
            'is_read' => false
        ]);

        $this->assertNotificationSent([3], NewMessageNotification::class, [
            BroadcastChannel::class,
            ExpoChannel::class
        ]);
    }

    public function testCreateMessageNewConversation()
    {
        Notification::fake();

        $response = $this->actingAs($this->secondUser)->json('post', '/messages', [
            'text' => 'New message',
            'recipient_id' => 3
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('created_message_to_second_user_response.json', $response->json());

        $this->assertDatabaseHas('conversations', [
            'id' => 4,
            'sender_id' => 2,
            'recipient_id' => 3
        ]);

        $this->assertNotificationSent([3], NewMessageNotification::class, [
            BroadcastChannel::class,
            ExpoChannel::class
        ]);
    }

    public function testCreateSelfMessage()
    {
        $response = $this->actingAs($this->user)->json('post', '/messages', [
            'text' => 'New message',
            'recipient_id' => 1
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $response->assertJson(['error' => 'You cannot send a message to yourself.']);
    }

    public function testCreateMessageWithoutAuth()
    {
        $response = $this->json('post', '/messages', [
            'text' => 'New message',
            'recipient_id' => 1
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testRead()
    {
        $response = $this->actingAs($this->secondUser)->json('put', '/messages/4/read');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('messages', ['id' => 1, 'is_read' => true]);
        $this->assertDatabaseHas('messages', ['id' => 4, 'is_read' => true]);
    }

    public function testReadNotExists()
    {
        $response = $this->actingAs($this->user)->json('put', '/messages/0/read');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $response->assertJson(['error' => 'Message does not exist.']);
    }

    public function testReadNotRecipient()
    {
        $response = $this->actingAs($this->user)->json('put', '/messages/1/read');

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $response->assertJson(['error' => 'You are not the recipient of this message.']);
    }

    public function testReadWithoutAuth()
    {
        $response = $this->json('put', '/messages/1/read');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getSearchFilters(): array
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_all.json'
            ],
            [
                'filter' => ['conversation_id' => 1],
                'result' => 'search_by_conversation.json'
            ],
            [
                'filter' => ['id_from' => 1, 'id_to' => 3],
                'result' => 'get_messages_from_id_one_to_id_three.json'
            ],
            [
                'filter' => ['created_at_from' => '2016-09-20 11:05:00', 'created_at_to' => '2017-10-20 11:05:00'],
                'result' => 'get_messages_from_to_datetime.json'
            ]
        ];
    }

    /**
     * @dataProvider  getSearchFilters
     *
     * @param array $filter
     * @param string $fixture
     */
    public function testSearch(array $filter, string $fixture)
    {
        $response = $this->actingAs($this->user)->json('get', '/messages', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testSearchWithoutAuth()
    {
        $response = $this->json('get', '/messages');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateMessageWithAttachment()
    {
        Notification::fake();

        $response = $this->actingAs($this->user)->json('post', '/messages', [
            'text' => 'Some message',
            'recipient_id' => 3,
            'attachment_id' => 3
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('created_message_to_fifth_user_with_attachment_response.json', $response->json());

        $this->assertDatabaseHas('messages', [
            'id' => 5,
            'conversation_id' => 3,
            'sender_id' => 1,
            'recipient_id' => 3,
            'text' => 'Some message',
            'is_read' => false,
            'attachment_id' => 3
        ]);

        $this->assertNotificationSent([3], NewMessageNotification::class, [
            BroadcastChannel::class,
            ExpoChannel::class
        ]);
    }
}
