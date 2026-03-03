<?php

namespace RonasIT\Chat\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use RonasIT\Chat\Contracts\Notifications\ConversationDeletedNotificationContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Enums\Conversation\TypeEnum;
use RonasIT\Chat\Repositories\ConversationRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @mixin ConversationRepository
 *
 * @property ConversationRepository $repository
 */
class ConversationService extends EntityService implements ConversationServiceContract
{
    public function __construct()
    {
        $this->setRepository(ConversationRepository::class);
    }

    public function getOrCreatePrivate(int $firstMemberId, int $secondMemberId): Model
    {
        $conversation = $this->getPrivateBetweenUsers($firstMemberId, $secondMemberId);

        if (empty($conversation)) {
            $conversation = $this->create(['type' => TypeEnum::Private]);

            $this->attachMembers($conversation, [$firstMemberId, $secondMemberId]);
        }

        return $conversation;
    }

    public function delete($where): void
    {
        $conversation = $this->with('members')->first($where);

        $this->repository->delete($where);

        $otherMembers = $conversation->members->filter(fn ($member) => $member->id !== Auth::id());

        $this->notifyUser($conversation->toArray(), $otherMembers);
    }

    public function notifyUser($conversation, $recipients): void
    {
        $conversationDeletedNotification = app(ConversationDeletedNotificationContract::class)
            ->setConversation($conversation);

        Notification::send($recipients, $conversationDeletedNotification);
    }

    public function search(array $filters = []): LengthAwarePaginator
    {
        if (Auth::check()) {
            $filters['member_id'] = Auth::id();
        }

        return $this
            ->searchQuery($filters)
            ->filterBy('members.member_id', 'member_id')
            ->getSearchResults();
    }

    public function getPrivateBetweenUsers(int $firstMemberId, int $secondMemberId): ?Model
    {
        return $this->getByTypeAndMembers(TypeEnum::Private, $firstMemberId, $secondMemberId);
    }
}
