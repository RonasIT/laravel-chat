<?php

namespace RonasIT\Chat\Notifications\Resources;

use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Illuminate\Http\Resources\MissingValue;
use RonasIT\Chat\Contracts\Notifications\Resources\NotificationResourceContract;

abstract class NotificationResource implements NotificationResourceContract
{
    use ConditionallyLoadsAttributes;

    public function __construct(
        protected readonly mixed $resource,
    ) {
    }

    public function isMissing(): bool
    {
        return $this->resource instanceof MissingValue;
    }

    abstract public function toArray(): array;

    public function jsonSerialize(): array
    {
        return $this->filter($this->toArray());
    }
}
