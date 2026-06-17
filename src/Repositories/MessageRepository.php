<?php

namespace RonasIT\Chat\Repositories;

use Illuminate\Database\Eloquent\Builder;
use RonasIT\Chat\Contracts\Models\MessageModelContract;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property MessageModelContract $model
 */
class MessageRepository extends BaseRepository
{
    protected ?int $withConversationIdentityForMemberId = null;

    public function __construct()
    {
        $this->setModel(app()->getAlias(MessageModelContract::class));

        $this->setAdditionalReservedFilters(
            'member_id',
            'with_conversation_identity',
        );
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

    public function withConversationIdentity(int $memberId): self
    {
        $this->withConversationIdentityForMemberId = $memberId;

        return $this;
    }

    protected function getQuery($where = []): Builder
    {
        $query = parent::getQuery($where);

        if (!is_null($this->withConversationIdentityForMemberId) && in_array('conversation', $this->attachedRelations)) {
            $memberId = $this->withConversationIdentityForMemberId;
            $query->with(['conversation' => fn ($query) => $query->withCalculatedIdentity($memberId)]);

            $this->withConversationIdentityForMemberId = null;
        }

        return $query;
    }
}
