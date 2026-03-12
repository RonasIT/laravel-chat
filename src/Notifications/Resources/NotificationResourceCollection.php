<?php

namespace RonasIT\Chat\Notifications\Resources;

use Illuminate\Http\Resources\MissingValue;
use Illuminate\Http\Resources\PotentiallyMissing;

abstract class NotificationResourceCollection extends NotificationResource implements PotentiallyMissing
{
    public string $collects;

    public function isMissing(): bool
    {
        return $this->resource instanceof MissingValue;
    }

    public function toArray(): array
    {
        return $this->resource->mapInto($this->collects)->toArray();
    }
}
