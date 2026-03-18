<?php

namespace RonasIT\Chat\Notifications\Resources\Broadcast;

use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Illuminate\Http\Resources\MissingValue;
use RonasIT\Chat\Contracts\Notifications\Resources\Broadcast\BroadcastResourceContract;

abstract class BroadcastResource implements BroadcastResourceContract
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

    public function jsonSerialize(): array
    {
        return $this->filter($this->toArray());
    }
}
