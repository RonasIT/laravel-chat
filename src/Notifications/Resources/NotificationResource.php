<?php

namespace RonasIT\Chat\Notifications\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use JsonSerializable;

abstract class NotificationResource implements Arrayable, JsonSerializable
{
    use ConditionallyLoadsAttributes;

    public function __construct(
        protected mixed $resource,
    ) {
    }

    abstract public function toArray(): array;

    public function jsonSerialize(): array
    {
        return $this->filter($this->toArray());
    }
}
