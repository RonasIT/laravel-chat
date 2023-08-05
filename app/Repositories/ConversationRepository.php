<?php

namespace App\Repositories;

use App\Models\Conversation;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property Conversation $model
 */
class ConversationRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Conversation::class);

        $this->setAdditionalReservedFilters(
            'owner_id',
            'with_unread_messages_count'
        );
    }

    public function getConversationBetweenUsers(int $senderId, int $recipientId): ?Conversation
    {
        return $this
            ->getQuery([
                'sender_id' => $senderId,
                'recipient_id' => $recipientId
            ])
            ->orWhere(function ($query) use ($senderId, $recipientId) {
                $query->where('sender_id', $recipientId);
                $query->where('recipient_id', $senderId);
            })
            ->first();
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

    public function withUnreadMessagesCount(): self
    {
        if (!empty($this->filter['with_unread_messages_count']) && !empty($this->filter['owner_id'])) {
            $this->query->withCount(['messages as unread_messages_count' => function ($query) {
                $query
                    ->where('recipient_id', $this->filter['owner_id'])
                    ->where('is_read', false);
            }]);
        }

        return $this;
    }
}
