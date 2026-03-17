<?php

namespace RonasIT\Chat\Notifications\Resources\Broadcast;

use RonasIT\Chat\Models\Conversation;

/**
 * @property Conversation $resource
 */
class ConversationResource extends BroadcastResource
{
    public function toArray(): array
    {
        return [
            'id' => $this->resource->id,
            'type' => $this->resource->type,
            'title' => $this->resource->title,
            'last_updated_at' => $this->resource->last_updated_at,
            'created_at' => $this->resource->created_at,
            'last_message' => new MessageResource($this->whenLoaded('last_message')),
            'pinned_messages' => new MessagesCollectionResource($this->whenLoaded('pinned_messages')),
            'members_count' => $this->whenCounted('members'),
            'unread_messages_count' => $this->whenHas('unread_messages_count'),
        ];
    }
}
