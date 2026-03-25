<?php

namespace RonasIT\Chat\Repositories;

use Illuminate\Database\Eloquent\Builder;
use RonasIT\Chat\Contracts\Models\ConversationModelContract;
use RonasIT\Chat\Enums\Conversation\TypeEnum;
use RonasIT\Support\Repositories\BaseRepository;

class ConversationRepository extends BaseRepository
{
    protected ?int $withUnreadCountMemberId = null;

    public function __construct()
    {
        $this->setModel(app()->getAlias(ConversationModelContract::class));

        $this->setAdditionalReservedFilters(
            'member_id',
            'with_unread_messages_count',
        );
    }

    public function getByTypeAndMembers(TypeEnum $type, int ...$membersIDs): ?ConversationModelContract
    {
        return $this
            ->getQuery(['type' => $type])
            ->whereHas('members', fn ($query) => $query->whereIn('member_id', $membersIDs), '>=', count($membersIDs))
            ->first();
    }

    public function attachMembers(ConversationModelContract $conversation, array $memberIds): void
    {
        $conversation->members()->attach($memberIds);
    }

    public function withUnreadCountMemberId(?int $memberId): self
    {
        $this->withUnreadCountMemberId = $memberId;

        return $this;
    }

    public function pinMessage(ConversationModelContract $conversation, int $messageId): array
    {
        return $conversation
            ->pinned_messages()
            ->syncWithoutDetaching([$messageId]);
    }

    public function unpinMessage(ConversationModelContract $conversation, int $messageId): int
    {
        return $conversation
            ->pinned_messages()
            ->detach([$messageId]);
    }

    protected function getQuery($where = []): Builder
    {
        $query = parent::getQuery($where);

        if (!is_null($this->withUnreadCountMemberId)) {
            $query->withUnreadMessagesCount($this->withUnreadCountMemberId);

            $this->withUnreadCountMemberId = null;
        }

        return $query;
    }
}
