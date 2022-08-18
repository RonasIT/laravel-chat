<?php

namespace RonasIT\Chat\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
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

    public function getOrCreateConversationBetweenUsers(int $senderId, int $recipientId): Model
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

    public function search(array $filters = []): LengthAwarePaginator
    {
        if (Auth::id()) {
            $filters['owner_id'] = Auth::id();
        }

        return $this
            ->with(Arr::get($filters, 'with', []))
            ->searchQuery($filters)
            ->filterByOwner()
            ->withUnreadMessagesCount()
            ->getSearchResults();
    }

    public function find(int $id, array $data): ?Model
    {
        return $this->repository->with(Arr::get($data, 'with', []))->find($id);
    }
}
