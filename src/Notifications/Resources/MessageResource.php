<?php

namespace RonasIT\Chat\Notifications\Resources;

use RonasIT\Chat\Contracts\Notifications\Resources\MessageNotificationResourceContract;

class MessageResource extends NotificationResource implements MessageNotificationResourceContract
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
