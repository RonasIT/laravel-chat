<?php

namespace RonasIT\Chat\Tests\Support\Attributes;

use Attribute;
use RonasIT\Chat\Enums\ChatRouteActionEnum;

#[Attribute(Attribute::TARGET_METHOD)]
final readonly class RegisterChatRoutes
{
    public array $actions;

    public function __construct(ChatRouteActionEnum ...$actions)
    {
        $this->actions = $actions;
    }
}
