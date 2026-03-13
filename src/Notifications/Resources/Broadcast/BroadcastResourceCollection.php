<?php

namespace RonasIT\Chat\Notifications\Resources\Broadcast;

abstract class BroadcastResourceCollection extends BroadcastResource
{
    public string $collects;

    public function toArray(): array
    {
        return $this->resource->mapInto($this->collects)->toArray();
    }
}
