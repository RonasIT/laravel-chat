<?php

namespace RonasIT\Chat\Repositories;

use RonasIT\Chat\Contracts\Models\MessageModelContract;
use RonasIT\Support\Repositories\BaseRepository;

class MessageRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(app()->getAlias(MessageModelContract::class));

        $this->setAdditionalReservedFilters('member_id');
    }

    public function getUnreadIdsByUser(int $conversationId, int $toMessageId, int $memberId): array
    {
        return $this
            ->getQuery(['conversation_id' => $conversationId])
            ->select('id')
            ->where('sender_id', '!=', $memberId)
            ->where('id', '<=', $toMessageId)
            ->whereDoesntHave('reads', fn ($query) => $query->where('read_messages.member_id', $memberId))
            ->orderBy('id')
            ->get()
            ->pluck('id')
            ->toArray();
    }
}
