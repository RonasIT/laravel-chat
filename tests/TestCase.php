<?php

namespace RonasIT\Chat\Tests;

use Carbon\Carbon;
use Dotenv\Dotenv;
use Illuminate\Notifications\Channels\BroadcastChannel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Orchestra\Testbench\TestCase as BaseTest;
use ReflectionClass;
use RonasIT\Chat\ChatServiceProvider;
use RonasIT\Chat\Tests\Models\User;
use RonasIT\Media\Models\Media;
use RonasIT\Support\Traits\FixturesTrait;

class TestCase extends BaseTest
{
    use FixturesTrait {
        getFixturePath as traitGetFixturePath;
    }

    protected bool $globalExportMode = false;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('chat.classes.user_model', User::class);
        Config::set('chat.classes.media_model', Media::class);
        Config::set('chat.default_channels.0', BroadcastChannel::class);

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        $this->loadTestDump();

        if (config('database.default') === 'pgsql') {
            $this->prepareSequences();
        }

        Carbon::setTestNow(Carbon::create(2024));
    }

    public function getFixturePath(string $fixtureName): string
    {
        $path = $this->traitGetFixturePath($fixtureName);

        return str_replace('vendor/orchestra/testbench-core/laravel/', '', $path);
    }

    protected function getEnvironmentSetUp($app): void
    {
        Dotenv::createImmutable(__DIR__ . '/..', '.env.testing')->load();

        $this->setupDb($app);
    }

    protected function getPackageProviders($app): array
    {
        return [
            ChatServiceProvider::class,
        ];
    }

    protected function setupDb($app): void
    {
        $app['config']->set('database.default', env('DB_DEFAULT', 'pgsql'));
        $app['config']->set('database.connections.pgsql', [
            'driver' => env('DB_DRIVER', 'pgsql'),
            'host' => env('DB_HOST', 'pgsql'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', 'secret'),
        ]);
    }

    protected function getObjectAttributes(object $object): array
    {
        $result = [];

        $properties = (new ReflectionClass($object))->getProperties();

        foreach ($properties as $property) {
            $value = $property->getValue($object);

            $result[$property->getName()] = $value;
        }

        return json_decode(json_encode($result), true);
    }

    protected function assertBroadcastNotificationSent(string $fixtureName, bool $exportMode = false): void
    {
        $notifications = Notification::sentNotifications();

        $actualData = [];

        foreach ($notifications as $notifiableIDs) {
            foreach ($notifiableIDs as $modelNotifications) {
                foreach ($modelNotifications as $notificationClassName => $modelNotification) {
                    foreach ($modelNotification as $notification) {
                        $broadcastNotification = $notification['notification'];

                        $notification['notification'] = $this->getObjectAttributes($notification['notification']);
                        $notification['notification']['broadcast_on'] = $broadcastNotification->broadcastOn();
                        $notification['notification']['broadcast_data'] = $broadcastNotification->toBroadcast()->data;

                        if (method_exists($broadcastNotification, 'broadcastAs')) {
                            $notification['notification']['broadcast_as'] = $broadcastNotification->broadcastAs();
                        }

                        unset($notification['notification']['id']);

                        $actualData[$notificationClassName][] = $notification;
                    }
                }
            }
        }

        $preparedActualData = json_decode(json_encode($actualData), true);

        $this->assertEqualsFixture("broadcast_notifications/{$fixtureName}", $preparedActualData, $exportMode);
    }
}
