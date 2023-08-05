<?php

namespace Tests;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use RonasIT\Support\AutoDoc\Tests\AutoDocTestCaseTrait;
use RonasIT\Support\Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use AutoDocTestCaseTrait;

    protected static bool $isJwtGuard;

    protected string $token;

    public function setUp(): void
    {
        parent::setUp();

        $defaultGuard = config('auth.defaults.guard');

        self::$isJwtGuard = config("auth.guards.{$defaultGuard}.driver") === 'jwt';
    }

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        /** @var Application $app */
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->loadEnvironmentFrom('.env.ci-testing');
        $app->make(Kernel::class)->bootstrap();

        $this->truncateExceptTables = ['migrations', 'password_resets', 'roles'];
        $this->prepareSequencesExceptTables = ['migrations', 'password_resets', 'settings', 'roles'];

        return $app;
    }

    public function actingAs(?Authenticatable $user, $guard = null): self
    {
        if (is_null($user)) {
            return $this;
        }

        if (!self::$isJwtGuard) {
            return parent::actingAs($user, $guard);
        }

        $this->token = Auth::fromUser($user);

        return $this;
    }

    public function actingAsByUsername(?string $userName, $guard = null): self
    {
        if (empty($userName)) {
            return $this;
        }

        return $this->actingAs($this->{$userName}, $guard);
    }

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): TestResponse
    {
        if (self::$isJwtGuard && !empty($this->token)) {
            $server['HTTP_AUTHORIZATION'] = "Bearer {$this->token}";
        }

        return parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }

    public function assertNotificationSent($userIds, $notificationClass, $channelsArray = []): void
    {
        $users = User::whereIn('id', $userIds)->get();

        Notification::assertSentTo($users, $notificationClass, function ($notification, $channels) use ($channelsArray) {
            return !array_diff($channels, $channelsArray);
        });
    }
}
