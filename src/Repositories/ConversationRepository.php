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
}
