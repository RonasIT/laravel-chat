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
    protected ?int $withConversationOverriddenTitleAndCoverMemberId = null;

    public function __construct()
    {
        $this->setModel(Message::class);

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

    public function withConversationOverriddenTitleAndCover(?int $memberId): self
    {
        $this->withConversationOverriddenTitleAndCoverMemberId = $memberId;

        return $this;
    }

    protected function getQuery($where = []): Builder
    {
        $query = parent::getQuery($where);

        if (!is_null($this->withConversationOverriddenTitleAndCoverMemberId)) {
            if (in_array('conversation', $this->attachedRelations)) {
                $memberId = $this->withConversationOverriddenTitleAndCoverMemberId;
                $query->with(['conversation' => fn ($query) => $query->withOverriddenTitleAndCover($memberId)]);
            }

            $this->withConversationOverriddenTitleAndCoverMemberId = null;
        }

        return $query;
    }
}
