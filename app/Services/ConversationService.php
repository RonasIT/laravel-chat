<?php

namespace App\Services;

use App\Contracts\Services\ConversationServiceContract;
use App\Notifications\ConversationDeletedNotification;
use App\Repositories\ConversationRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
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

    public function delete($where): void
    {
        $conversation = $this->with(['recipient', 'sender'])->first($where);

        $this->repository->delete($where);

        $this->notifyUser($conversation, collect([$conversation->recipient, $conversation->sender]));
    }

    public function notifyUser($conversation, $recipients): void
    {
        Notification::send($recipients, new ConversationDeletedNotification($conversation->toArray()));
    }

    public function search(array $filters = []): LengthAwarePaginator
    {
        if (Auth::check()) {
            $filters['owner_id'] = Auth::id();
        }

        return $this
            ->searchQuery($filters)
            ->filterByOwner()
            ->withUnreadMessagesCount()
            ->getSearchResults();
    }
}
