<?php

namespace RonasIT\Chat\Http\Resources;

use Illuminate\Http\Request;
use RonasIT\Chat\Contracts\Resources\ConversationResourceContract;
use RonasIT\Media\Http\Resources\MediaResource;
use RonasIT\Support\Http\BaseResource;

class ConversationResource extends BaseResource implements ConversationResourceContract
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'last_updated_at' => $this->resource->last_updated_at,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'creator_id' => $this->resource->creator_id,
            'type' => $this->resource->type,
            'title' => $this->resource->title,
            'cover_id' => $this->resource->cover_id,
            'messages' => MessagesCollectionResource::make($this->whenLoaded('messages')),
            'creator' => $this->whenLoaded('creator'),
            'members' => $this->whenLoaded('members'),
            'last_message' => MessageResource::make($this->whenLoaded('last_message')),
            'cover' => MediaResource::make($this->whenLoaded('cover')),
            'pinned_messages' => MessagesCollectionResource::make($this->whenLoaded('pinned_messages')),
            'members_count' => $this->whenCounted('members'),
            'unread_messages_count' => $this->whenHas('unread_messages_count'),
        ];
    }
}
