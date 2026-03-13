<?php

namespace RonasIT\Chat\Notifications\Resources\Broadcast;

class MessageResource extends BroadcastResource
{
    public function toArray(): array
    {
        return [
            'id' => $this->resource->id,
            'text' => $this->resource->text,
            'is_read' => $this->resource->is_read,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
