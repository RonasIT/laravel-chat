<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Route;
use RonasIT\Chat\Enums\ChatRouteActionEnum;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Tests\Models\User;
use RonasIT\Chat\Tests\Support\ModelTestState;

class ConversationStaticTest extends TestCase
{
    protected static User $sender;
    protected static User $recipient;
    protected static User $someAuthUser;

    protected static ModelTestState $conversationTestState;

    public function setUp(): void
    {
        parent::setUp();

        self::$sender ??= User::find(1);
        self::$recipient ??= User::find(2);
        self::$someAuthUser ??= User::find(3);

        self::$conversationTestState = new ModelTestState(Conversation::class);
    }

    public function testEverythingDisabledExceptSearch(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationSearch);

        $responseSearch = $this->actingAs(self::$sender)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$sender)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$sender)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$sender)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$sender)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$sender)->postJson('/messages');
        $responseRead = $this->actingAs(self::$sender)->putJson('messages/1/read');

        $responseSearch->assertOk();

        $responseGet->assertNotFound();
        $responseDelete->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testEverythingDisabledExceptDelete(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationDelete);

        $responseSearch = $this->actingAs(self::$sender)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$sender)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$sender)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$sender)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$sender)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$sender)->postJson('/messages');
        $responseRead = $this->actingAs(self::$sender)->putJson('messages/1/read');

        $responseDelete->assertNoContent();

        $responseGet->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testEverythingDisabledExceptGet(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $responseSearch = $this->actingAs(self::$sender)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$sender)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$sender)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$sender)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$sender)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$sender)->postJson('/messages');
        $responseRead = $this->actingAs(self::$sender)->putJson('messages/1/read');

        $responseGet->assertOk();

        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGetByUser->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testEverythingDisabledExceptGetByUser(): void
    {
        Route::chat(ChatRouteActionEnum::ConversationGetByUser);

        $responseSearch = $this->actingAs(self::$sender)->getJson('/conversations');
        $responseGet = $this->actingAs(self::$sender)->getJson('/conversations/1');
        $responseDelete = $this->actingAs(self::$sender)->deleteJson('/conversations/1');
        $responseGetByUser = $this->actingAs(self::$sender)->getJson('/users/2/conversation');
        $responseSearchMessages = $this->actingAs(self::$sender)->getJson('/messages');
        $responseCreate = $this->actingAs(self::$sender)->postJson('/messages');
        $responseRead = $this->actingAs(self::$sender)->putJson('messages/1/read');

        $responseGetByUser->assertOk();

        $responseDelete->assertNotFound();
        $responseSearch->assertNotFound();
        $responseGet->assertNotFound();
        $responseSearchMessages->assertNotFound();
        $responseCreate->assertNotFound();
        $responseRead->assertNotFound();
    }

    public function testGetBySender()
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $response = $this->actingAs(self::$sender)->json('get', '/conversations/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation.json', $response->json());
    }

    public function testGetByRecipient()
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $response = $this->actingAs(self::$sender)->json('get', '/conversations/1');

        $response->assertOk();

        $this->assertEqualsFixture('get_conversation.json', $response->json());
    }

    public function testGetBySomeUser()
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $response = $this->actingAs(self::$someAuthUser)->json('get', '/conversations/1');

        $response->assertForbidden();

        $response->assertJson(['message' => 'You are not the owner of this Conversation.']);
    }

    public function testGetNotExists()
    {
        Route::chat(ChatRouteActionEnum::ConversationGet);

        $response = $this->actingAs(self::$sender)->json('get', '/conversations/0');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Conversation does not exist']);
    }

    public function testGetGetDisabled()
    {
        $response = $this->json('get', '/conversations/1');

        $response->assertNotFound();

        $response->assertJson(['message' => 'Not found.']);
    }
}
