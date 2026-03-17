<?php

namespace RonasIT\Chat\Notifications\Resources\Broadcast;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Http\Resources\PotentiallyMissing;
use JsonSerializable;

abstract class BroadcastResource implements Arrayable, JsonSerializable, PotentiallyMissing
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
