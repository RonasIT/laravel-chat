<?php

namespace RonasIT\Chat\Notifications\Resources;

abstract class NotificationResourceCollection extends NotificationResource
{
    public string $collects;

    public function toArray(): array
    {
        return $this->resource->map(fn ($item) => app($this->collects, ['resource' => $item]))->toArray();
    }
}
