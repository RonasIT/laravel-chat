<?php

namespace RonasIT\Chat\Repositories;

use RonasIT\Chat\Enums\Conversation\TypeEnum;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property Conversation $model
 */
class ConversationRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Conversation::class);

        $this->setAdditionalReservedFilters('member_id');
    }

    public function getPrivateBetweenUsers(int $firstMemberId, int $secondMemberId): ?Conversation
    {
        return $this
            ->getQuery(['type' => TypeEnum::Private->value])
            ->whereHas('members', fn ($query) => $query->where('member_id', $firstMemberId))
            ->whereHas('members', fn ($query) => $query->where('member_id', $secondMemberId))
            ->first();
    }

    public function attachMembers(Conversation $conversation, array $memberIds): void
    {
        $conversation->members()->attach($memberIds);
    }
}
