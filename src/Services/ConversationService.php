<?php

namespace RonasIT\Chat\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Repositories\ConversationRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @mixin ConversationRepository
 * @property ConversationRepository $repository
 */
class ConversationService extends EntityService implements ConversationServiceContract
{
    public function __construct()
    {
        $this->setRepository(ConversationRepository::class);
    }

    public function getOrCreateConversationBetweenUsers($senderId, $recipientId)
    {
        $conversation = $this->getConversationBetweenUsers($senderId, $recipientId);

        if (empty($conversation)) {
            return $this->create([
                'sender_id' => $senderId,
                'recipient_id' => $recipientId,
            ]);
        }

        return $conversation;
    }

    public function search($filters)
    {
        if (!empty(Auth::user())) {
            $filters['owner_id'] = Auth::user()->id;
        }

        return $this
            ->repository
            ->with(Arr::get($filters, 'with', []))
            ->searchQuery($filters)
            ->filterByOwner()
            ->withUnreadMessagesCount()
            ->getSearchResults();
    }

    public function get($id, $data)
    {
        $with = Arr::get($data, 'with', []);

        return $this->repository
            ->with($with)
            ->find($id);
    }
}
