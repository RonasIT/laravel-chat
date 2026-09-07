<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Notifications\Channels\BroadcastChannel;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\DataProvider;
use RonasIT\Chat\Contracts\Notifications\ConversationCreatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\ConversationDeletedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\ConversationUpdatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\MessageCreatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\MessageUpdatedNotificationContract;
use RonasIT\Chat\Tests\Models\User;
use RonasIT\Chat\Tests\Support\Channels\CustomBroadcastChannel;
use RonasIT\Chat\Tests\Support\Enums\QueueEnum;
use RonasIT\Chat\Tests\Support\Notifications\CustomMessageCreatedNotification;

class NotificationQueueTest extends TestCase
{
    const string QUEUE_NAME = 'chat';
    const string NOTIFICATION_QUEUE_NAME = 'custom';

    protected bool $isNotificationFaked = false;

    protected static User $recipient;

    public function setUp(): void
    {
        parent::setUp();

        self::$recipient ??= User::find(1);
    }

    public function testConfigIsMergedForApplicationWithoutPublishedConfig(): void
    {
        $this->assertTrue(Config::has('chat.broadcast_queue'));
        $this->assertNull(config('chat.broadcast_queue'));
    }

    #[DataProvider('getNotifications')]
    public function testSendWithoutConfiguredQueue(string $contract, array $arguments): void
    {
        Bus::fake();

        $notification = app($contract, $arguments);

        self::$recipient->notify($notification);

        Bus::assertDispatched(
            command: SendQueuedNotifications::class,
            callback: fn (SendQueuedNotifications $job) => $job->queue === null,
        );

        $this->assertNull($notification->toBroadcast()->queue);
    }

    #[DataProvider('getNotifications')]
    public function testSendWithConfiguredQueue(string $contract, array $arguments): void
    {
        Config::set('chat.broadcast_queue', self::QUEUE_NAME);

        Bus::fake();

        $notification = app($contract, $arguments);

        self::$recipient->notify($notification);

        Bus::assertDispatched(
            command: SendQueuedNotifications::class,
            callback: fn (SendQueuedNotifications $job) => $job->queue === self::QUEUE_NAME,
        );

        $this->assertEquals(self::QUEUE_NAME, $notification->toBroadcast()->queue);
    }

    #[DataProvider('getNotifications')]
    public function testBroadcastEventPushedToConfiguredQueue(string $contract, array $arguments): void
    {
        Config::set('chat.broadcast_queue', self::QUEUE_NAME);

        Queue::fake();

        $notification = app($contract, $arguments);

        app(BroadcastChannel::class)->send(self::$recipient, $notification);

        Queue::assertPushedOn(self::QUEUE_NAME, BroadcastEvent::class);
    }

    public function testBroadcastEventPushedToDefaultQueue(): void
    {
        Queue::fake();

        $notification = app(MessageCreatedNotificationContract::class, [
            'messageId' => 1,
            'recipientId' => 1,
        ]);

        app(BroadcastChannel::class)->send(self::$recipient, $notification);

        Queue::assertPushed(BroadcastEvent::class, fn ($job, $queue) => $queue === null);
    }

    public function testSendToExtraChannel(): void
    {
        Config::set('chat.default_channels', [BroadcastChannel::class, DatabaseChannel::class]);
        Config::set('chat.broadcast_queue', self::QUEUE_NAME);

        Bus::fake();

        $notification = app(MessageCreatedNotificationContract::class, [
            'messageId' => 1,
            'recipientId' => 1,
        ]);

        self::$recipient->notify($notification);

        Bus::assertDispatchedTimes(SendQueuedNotifications::class, 2);

        Bus::assertDispatched(
            command: SendQueuedNotifications::class,
            callback: fn (SendQueuedNotifications $job) => $job->channels === [BroadcastChannel::class]
                && $job->queue === self::QUEUE_NAME,
        );

        Bus::assertDispatched(
            command: SendQueuedNotifications::class,
            callback: fn (SendQueuedNotifications $job) => $job->channels === [DatabaseChannel::class]
                && $job->queue === self::QUEUE_NAME,
        );
    }

    public function testSendToReplacedChannelClass(): void
    {
        Config::set('chat.default_channels', [CustomBroadcastChannel::class]);
        Config::set('chat.broadcast_queue', self::QUEUE_NAME);

        Bus::fake();

        $notification = app(MessageCreatedNotificationContract::class, [
            'messageId' => 1,
            'recipientId' => 1,
        ]);

        self::$recipient->notify($notification);

        Bus::assertDispatched(
            command: SendQueuedNotifications::class,
            callback: fn (SendQueuedNotifications $job) => $job->channels === [CustomBroadcastChannel::class]
                && $job->queue === self::QUEUE_NAME,
        );
    }

    public function testSendWithoutBroadcastQueueConfigKey(): void
    {
        Config::set('chat', Arr::except(config('chat'), 'broadcast_queue'));

        Bus::fake();

        $notification = app(MessageCreatedNotificationContract::class, [
            'messageId' => 1,
            'recipientId' => 1,
        ]);

        self::$recipient->notify($notification);

        Bus::assertDispatched(
            command: SendQueuedNotifications::class,
            callback: fn (SendQueuedNotifications $job) => $job->queue === null,
        );

        $this->assertNull($notification->toBroadcast()->queue);
    }

    public function testSendWithQueueSetOnNotification(): void
    {
        Bus::fake();

        $notification = app(MessageCreatedNotificationContract::class, [
            'messageId' => 1,
            'recipientId' => 1,
        ]);

        self::$recipient->notify($notification->onQueue(self::NOTIFICATION_QUEUE_NAME));

        Bus::assertDispatched(
            command: SendQueuedNotifications::class,
            callback: fn (SendQueuedNotifications $job) => $job->queue === self::NOTIFICATION_QUEUE_NAME,
        );

        $this->assertNull($notification->toBroadcast()->queue);
    }

    public function testSendWithConfiguredQueueOverridingQueueSetOnNotification(): void
    {
        Config::set('chat.broadcast_queue', self::QUEUE_NAME);

        Bus::fake();

        $notification = app(MessageCreatedNotificationContract::class, [
            'messageId' => 1,
            'recipientId' => 1,
        ]);

        self::$recipient->notify($notification->onQueue(self::NOTIFICATION_QUEUE_NAME));

        Bus::assertDispatched(
            command: SendQueuedNotifications::class,
            callback: fn (SendQueuedNotifications $job) => $job->queue === self::QUEUE_NAME,
        );

        $this->assertEquals(self::QUEUE_NAME, $notification->toBroadcast()->queue);
    }

    public function testSendWithQueueOverriddenOnNotification(): void
    {
        Config::set('chat.broadcast_queue', self::QUEUE_NAME);

        $this->app->bind(
            abstract: MessageCreatedNotificationContract::class,
            concrete: CustomMessageCreatedNotification::class,
        );

        Bus::fake();

        $notification = app(MessageCreatedNotificationContract::class, [
            'messageId' => 1,
            'recipientId' => 1,
        ]);

        self::$recipient->notify($notification);

        Bus::assertDispatched(
            command: SendQueuedNotifications::class,
            callback: fn (SendQueuedNotifications $job) => $job->queue === CustomMessageCreatedNotification::QUEUE_NAME,
        );

        $this->assertEquals(
            expected: CustomMessageCreatedNotification::QUEUE_NAME,
            actual: $notification->toBroadcast()->queue,
        );
    }

    public function testSendWithConfiguredQueueAsEnum(): void
    {
        Config::set('chat.broadcast_queue', QueueEnum::Chat);

        Bus::fake();

        $notification = app(MessageCreatedNotificationContract::class, [
            'messageId' => 1,
            'recipientId' => 1,
        ]);

        self::$recipient->notify($notification);

        Bus::assertDispatched(
            command: SendQueuedNotifications::class,
            callback: fn (SendQueuedNotifications $job) => $job->queue === QueueEnum::Chat->value,
        );

        $this->assertEquals(QueueEnum::Chat->value, $notification->toBroadcast()->queue);
    }

    public static function getNotifications(): array
    {
        return [
            'message_created' => [
                MessageCreatedNotificationContract::class,
                ['messageId' => 1, 'recipientId' => 1],
            ],
            'message_updated' => [
                MessageUpdatedNotificationContract::class,
                ['messageId' => 1, 'recipientId' => 1],
            ],
            'conversation_created' => [
                ConversationCreatedNotificationContract::class,
                ['conversationId' => 1, 'recipientId' => 1],
            ],
            'conversation_updated' => [
                ConversationUpdatedNotificationContract::class,
                ['conversationId' => 1, 'recipientId' => 1],
            ],
            'conversation_deleted' => [
                ConversationDeletedNotificationContract::class,
                ['conversationId' => 1, 'recipientId' => 1],
            ],
        ];
    }
}
