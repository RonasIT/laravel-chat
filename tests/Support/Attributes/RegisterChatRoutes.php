<?php

namespace RonasIT\Chat\Tests\Support\Attributes;

use Attribute;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Contracts\Attributes\Invokable;
use RonasIT\Chat\Enums\ChatRouteActionEnum;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class RegisterChatRoutes implements Invokable
{
    public array $actions;

    public function __construct(ChatRouteActionEnum ...$actions)
    {
        $this->actions = $actions;
    }

    public function __invoke($app): void
    {
        Route::chat(...$this->actions);
    }
}
