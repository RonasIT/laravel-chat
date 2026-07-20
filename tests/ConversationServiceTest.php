<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Notification;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Tests\Models\User;
use RonasIT\Chat\Tests\Support\ModelTestState;
use RonasIT\Chat\Tests\Support\TableTestState;

class ConversationServiceTest extends TestCase
{
    protected static User $currentUser;

    protected static ModelTestState $conversationState;
    protected static TableTestState $conversationMemberState;

    public function setUp(): void
    {
        parent::setUp();

        self::$currentUser ??= User::find(1);

        self::$conversationState = new ModelTestState(Conversation::class);
        self::$conversationMemberState = new TableTestState('conversation_member');
    }

    public function testDeleteByList(): void
    {
        $this->actingAs(self::$currentUser);

        app(ConversationServiceContract::class)->deleteByList([1, 6]);

        $this->assertBroadcastNotificationSent('delete_by_list');

        self::$conversationState->assertChangesEqualsFixture('deleted_by_list');
        self::$conversationMemberState->assertChangesEqualsFixture('deleted_by_list');
    }

    public function testDeleteByListWithNonExistingIds(): void
    {
        $this->actingAs(self::$currentUser);

        app(ConversationServiceContract::class)->deleteByList([2, 999]);

        $this->assertBroadcastNotificationSent('delete_by_list_with_non_existing_ids');

        self::$conversationState->assertChangesEqualsFixture('deleted_by_list_with_non_existing_ids');
        self::$conversationMemberState->assertChangesEqualsFixture('deleted_by_list_with_non_existing_ids');
    }

    public function testDeleteByListEmpty(): void
    {
        $this->actingAs(self::$currentUser);

        app(ConversationServiceContract::class)->deleteByList([]);

        Notification::assertNothingSent();

        self::$conversationState->assertNotChanged();
        self::$conversationMemberState->assertNotChanged();
    }

    public function testDeleteByListByField(): void
    {
        $this->actingAs(self::$currentUser);

        app(ConversationServiceContract::class)->deleteByList([1, 4], 'creator_id');

        $this->assertBroadcastNotificationSent('delete_by_list_by_field');

        self::$conversationState->assertChangesEqualsFixture('deleted_by_list_by_field');
        self::$conversationMemberState->assertChangesEqualsFixture('deleted_by_list_by_field');
    }
}
