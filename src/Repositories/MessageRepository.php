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

    public function getUnreadMessageIdsByUser(int $conversationId, string $messageCreatedAt, int $memberId): array
    {
        return $this
            ->getQuery(['conversation_id' => $conversationId])
            ->select('id')
            ->where('sender_id', '!=', $memberId)
            ->where('created_at', '<=', $messageCreatedAt)
            ->whereDoesntHave('members_who_read_message', fn ($query) => $query->where('read_messages.member_id', $memberId))
            ->orderBy('id')
            ->get()
            ->pluck('id')
            ->toArray();
    }

    protected function getQuery($where = []): Builder
    {
        $query = parent::getQuery($where);

        return $query->withIsRead();
    }
}
