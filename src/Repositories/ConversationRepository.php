<?php

namespace RonasIT\Chat\Repositories;

use Illuminate\Database\Eloquent\Builder;
use RonasIT\Chat\Enums\Conversation\TypeEnum;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property Conversation $model
 */
class ConversationRepository extends BaseRepository
{
    protected ?int $withUnreadMessagesCountMemberId = null;

    public function __construct()
    {
        $this->setModel(Conversation::class);

        $this->setAdditionalReservedFilters(
            'member_id',
            'with_unread_messages_count',
        );
    }

    public function getByTypeAndMembers(TypeEnum $type, int ...$membersIDs): ?Conversation
    {
        return $this
            ->getQuery(['type' => $type])
            ->whereHas('members', fn ($query) => $query->whereIn('member_id', $membersIDs), '>=', count($membersIDs))
            ->first();
    }

    public function attachMembers(Conversation $conversation, array $memberIds): void
    {
        $conversation->members()->attach($memberIds);
    }

    public function setWithUnreadMessagesCountMemberId(?int $memberId): self
    {
        $this->withUnreadMessagesCountMemberId = $memberId;

        return $this;
    }

    protected function getQuery($where = []): Builder
    {
        $query = parent::getQuery($where);

        if (!is_null($this->withUnreadMessagesCountMemberId)) {
            $query->withUnreadMessagesCount($this->withUnreadMessagesCountMemberId);
        }

        return $query;
    }
}
