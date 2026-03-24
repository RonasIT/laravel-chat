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
    protected ?int $withOverridenTitleAndCoverMemberId = null;
    protected ?int $withUnreadCountMemberId = null;

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

    public function withOverridenTitleAndCover(?int $memberId): self
    {
        $this->withOverridenTitleAndCoverMemberId = $memberId;

        return $this;
    }

    public function withUnreadCountMemberId(?int $memberId): self
    {
        $this->withUnreadCountMemberId = $memberId;

        return $this;
    }

    public function pinMessage(Conversation $conversation, int $messageId): array
    {
        return $conversation
            ->pinned_messages()
            ->syncWithoutDetaching([$messageId]);
    }

    public function unpinMessage(Conversation $conversation, int $messageId): int
    {
        return $conversation
            ->pinned_messages()
            ->detach([$messageId]);
    }

    protected function getQuery($where = []): Builder
    {
        $query = parent::getQuery($where);

        if (!is_null($this->withOverridenTitleAndCoverMemberId)) {
            $query->withOverridenTitleAndCover($this->withOverridenTitleAndCoverMemberId);

            $this->withOverridenTitleAndCoverMemberId = null;
        }

        if (!is_null($this->withUnreadCountMemberId)) {
            $query->withUnreadMessagesCount($this->withUnreadCountMemberId);

            $this->withUnreadCountMemberId = null;
        }

        return $query;
    }
}
