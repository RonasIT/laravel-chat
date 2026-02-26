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

        $this->setAdditionalReservedFilters(
            'member_id',
            'with_unread_messages_count',
        );
    }

    public function getPrivateByMembers(int $firstMemberId, int $secondMemberId): ?Conversation
    {
        return $this
            ->getQuery(['type' => TypeEnum::Private])
            ->whereHas('members', function ($query) use ($firstMemberId) {
                $query->where('member_id', $firstMemberId);
            })
            ->whereHas('members', function ($query) use ($secondMemberId) {
                $query->where('member_id', $secondMemberId);
            })
            ->first();
    }

    public function withUnreadMessagesCount(): self
    {
        if (!empty($this->filter['with_unread_messages_count']) && !empty($this->filter['member_id'])) {
            $memberId = $this->filter['member_id'];

            $this->query->withCount(['messages as unread_messages_count' => function ($query) use ($memberId) {
                $query
                    ->whereNot('sender_id', $memberId)
                    ->whereRaw(
                        'messages.id > COALESCE((SELECT last_read_message_id FROM conversation_member WHERE conversation_id = messages.conversation_id AND member_id = ?), 0)',
                        [$memberId],
                    );
            }]);
        }

        return $this;
    }

    public function attachMembers(Conversation $conversation, array $members): void
    {
        $conversation->members()->attach($members);
    }
}
