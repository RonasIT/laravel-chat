<?php

namespace Tests;

use App\Models\User;
use App\Notifications\ConversationDeletedNotification;
use Illuminate\Notifications\Channels\BroadcastChannel;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;

class ConversationTest extends TestCase
{
    protected $user;
    protected $notOwner;

    public function setUp() : void
    {
        parent::setUp();

        $this->user = User::find(1);
        $this->notOwner = User::find(3);
    }

    public function testGetConversationByUserId()
    {
        $response = $this->actingAs($this->user)->json('get', '/users/3/conversation');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_third_conversation.json', $response->json());
    }

    public function testGet()
    {
        $response = $this->actingAs($this->user)->json('get', '/conversations/1');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_first_conversation.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->user)->json('get', '/conversations/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $response->assertJson(['error' => 'Conversation does not exist.']);
    }

    public function testGetNotOwner()
    {
        $response = $this->actingAs($this->notOwner)->json('get', '/conversations/1');

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $response->assertJson(['error' => 'You are not the owner of this Conversation.']);
    }

    public function testGetNoAuth()
    {
        $response = $this->json('get', '/conversations/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDelete()
    {
        Notification::fake();

        $response = $this->actingAs($this->user)->json('delete', '/conversations/1');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('conversations', ['id' => 1]);

        $this->assertNotificationSent([1, 2], ConversationDeletedNotification::class, [BroadcastChannel::class]);
    }

    public function testDeleteNotExists()
    {
        $response = $this->actingAs($this->user)->json('delete', '/conversations/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $response->assertJson(['error' => 'Conversation does not exist.']);
    }

    public function testDeleteNotOwner()
    {
        $response = $this->actingAs($this->notOwner)->json('delete', '/conversations/1');

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $response->assertJson(['error' => 'You are not the owner of this Conversation.']);
    }

    public function testDeleteNoAuth()
    {
        $response = $this->json('delete', '/conversations/1');

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
                'filter' => ['with_unread_messages_count' => true],
                'result' => 'search_with_unread_messages_count.json'
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
        $response = $this->actingAs($this->user)->json('get', '/conversations', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testSearchWithoutAuth()
    {
        $response = $this->json('get', '/conversations');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
