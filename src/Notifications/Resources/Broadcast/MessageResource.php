<?php

namespace RonasIT\Chat\Notifications\Resources\Broadcast;

use RonasIT\Chat\Models\Message;

/**
 * @property Message $resource
 */
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
            'sender' => $this->whenLoaded('sender'),
        ];
    }
}
