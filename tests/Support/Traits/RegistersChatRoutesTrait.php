<?php

namespace RonasIT\Chat\Tests\Support\Traits;

use Illuminate\Support\Facades\Route;
use ReflectionMethod;
use RonasIT\Chat\Tests\Support\Attributes\RegisterChatRoutes;

trait RegistersChatRoutesTrait
{
    protected function defineRoutes($router): void
    {
        $methodReflection = new ReflectionMethod($this, $this->name());

        $attributes = $methodReflection->getAttributes(RegisterChatRoutes::class);

        foreach ($attributes as $attribute) {
            Route::chat(...$attribute->newInstance()->actions);
        }
    }
}
