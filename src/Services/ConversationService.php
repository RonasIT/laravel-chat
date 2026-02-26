<?php

namespace RonasIT\Chat\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use RonasIT\Chat\Contracts\Notifications\ConversationDeletedNotificationContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Enums\Conversation\TypeEnum;
use RonasIT\Chat\Models\Conversation;
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

    public function getOrCreatePrivate(int $firstMemberId, int $secondMemberId): Conversation
    {
        $conversation = $this->getPrivateByMembers($firstMemberId, $secondMemberId);

        if (empty($conversation)) {
            $conversation = $this->create(['type' => TypeEnum::Private]);

            $this->attachMembers($conversation, [$firstMemberId, $secondMemberId]);
        }

        return $conversation;
    }

    public function update(int $id, array $data): ?Conversation
    {
        return DB::transaction(function () use ($id, $data) {
            $conversation = $this->repository->update($id, $data);

            if (Arr::has($data, 'member_ids')) {
                $conversation->members()->sync($data['member_ids']);
            }

            return $conversation;
        });

        //TODO: remove old cover from media table
    }

    public function delete($where): void
    {
        $conversation = $this->with('members')->first($where);

        $this->repository->delete($where);

        $this->notifyUser($conversation->toArray(), $conversation->members);
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
            ->withUnreadMessagesCount()
            ->getSearchResults();
    }
}
