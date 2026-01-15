<?php

namespace RonasIT\Chat\Repositories;

use RonasIT\Chat\Models\Message;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property Message $model
 */
class MessageRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Message::class);

        $this->setAdditionalReservedFilters(
            'owner_id',
        );
    }

    public function filterByOwner(): self
    {
        if (!empty($this->filter['owner_id'])) {
            $this->query->where(function ($query) {
                $query
                    ->orWhere('sender_id', $this->filter['owner_id'])
                    ->orWhere('recipient_id', $this->filter['owner_id']);
            });
        }

        return $this;
    }

    public function markAsReadMessages($recipientId, $fromMessageId): int
    {
        return $this
            ->getQuery()
            ->where('conversation_id', function ($query) use ($fromMessageId) {
                $query
                    ->select('conversation_id')
                    ->from('messages')
                    ->where('id', $fromMessageId);
            })
            ->where('recipient_id', $recipientId)
            ->where('id', '<=', $fromMessageId)
            ->update(['is_read' => true]);
    }
}
