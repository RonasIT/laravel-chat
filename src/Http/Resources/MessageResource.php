<?php

namespace RonasIT\Chat\Http\Resources;

use Illuminate\Http\Request;
use RonasIT\Chat\Contracts\Resources\MessageResourceContract;
use RonasIT\Media\Http\Resources\MediaResource;
use RonasIT\Support\Http\BaseResource;

class MessageResource extends BaseResource implements MessageResourceContract
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'text' => $this->resource->text,
            'is_read' => $this->resource->is_read,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'conversation' => ConversationResource::make($this->whenLoaded('conversation')),
            'sender' => $this->whenLoaded('sender'),
            'attachment' => MediaResource::make($this->whenLoaded('attachment')),
        ];
    }
}
