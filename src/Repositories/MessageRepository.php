<?php

namespace RonasIT\Chat\Repositories;

use Illuminate\Database\Eloquent\Builder;
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

        $this->setAdditionalReservedFilters('member_id');
    }

    public function getQuery($where = []): Builder
    {
        $query = parent::getQuery($where);

        return $query->withIsRead();
    }

    public function markAsRead(Message $message, int $userId): void
    {
        $message
            ->conversation
            ->members()
            ->updateExistingPivot($userId, ['last_read_message_id' => $message->id]);
    }
}
