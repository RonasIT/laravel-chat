<?php

namespace RonasIT\Chat\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RonasIT\Chat\Contracts\Notifications\ConversationCreatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\ConversationDeletedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\ConversationUpdatedNotificationContract;
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

    public function getOrCreatePrivate(int $firstMemberId, int $secondMemberId): Model
    {
        $conversation = $this->getPrivate($firstMemberId, $secondMemberId);

        if (empty($conversation)) {
            $conversation = $this->create(['type' => TypeEnum::Private], [$firstMemberId, $secondMemberId]);
        }

        return $conversation;
    }

    public function create(array $data, array $members = []): Conversation
    {
        $conversation = DB::transaction(function () use ($data, $members) {
            $conversation = $this->repository->create($data);

            if (!empty($members)) {
                $this->attachMembers($conversation, $members);
            }

            return $conversation;
        });

        $this->postCreateHook($conversation);

        return $conversation;
    }

    public function update(array|int $where, array $data): ?Conversation
    {
        $updated = $this->repository->update($where, $data);

        if (!is_null($updated)) {
            $this->postUpdateHook($updated);
        }

        return $updated;
    }

    public function pinMessage(Conversation $conversation, int $messageId): void
    {
        $result = $this->repository->pinMessage($conversation, $messageId);

        if (!empty($result['attached'])) {
            $this->postUpdateHook($conversation);
        }
    }

    public function unpinMessage(Conversation $conversation, int $messageId): void
    {
        $unpinnedCount = $this->repository->unpinMessage($conversation, $messageId);

        if ($unpinnedCount > 0) {
            $this->postUpdateHook($conversation);
        }
    }

    public function delete($where): void
    {
        $conversation = $this->with('members')->first($where);

        $this->repository->delete($where);

        $this->postDeleteHook($conversation);
    }

    public function search(array $filters = []): LengthAwarePaginator
    {
        if (Auth::check()) {
            $filters['member_id'] = Auth::id();
        }

        $forMemberId = (Arr::get($filters, 'with_unread_messages_count', false))
            ? Arr::get($filters, 'member_id')
            : null;

        return $this
            ->withOverriddenTitleAndCover(Arr::get($filters, 'member_id'))
            ->withUnreadCountMemberId($forMemberId)
            ->searchQuery($filters)
            ->filterBy('members.member_id', 'member_id')
            ->getSearchResults();
    }

    public function retrieveById(int $id): ?Model
    {
        return $this
            ->withOverriddenTitleAndCover(Auth::id())
            ->repository->find($id);
    }

    public function getPrivate(int $firstMemberId, int $secondMemberId): ?Model
    {
        return $this
            ->withOverriddenTitleAndCover($firstMemberId)
            ->getByTypeAndMembers(TypeEnum::Private, $firstMemberId, $secondMemberId);
    }

    protected function sendCreatedNotifications(Conversation $conversation, Collection $recipients): void
    {
        $this->sendNotifications($conversation, $recipients, ConversationCreatedNotificationContract::class);
    }

    protected function sendUpdatedNotifications(Conversation $conversation, Collection $recipients): void
    {
        $this->sendNotifications($conversation, $recipients, ConversationUpdatedNotificationContract::class);
    }

    protected function sendDeletedNotifications(Conversation $conversation, Collection $recipients): void
    {
        $this->sendNotifications($conversation, $recipients, ConversationDeletedNotificationContract::class);
    }

    protected function sendNotifications(Conversation $conversation, Collection $recipients, string $notificationClass): void
    {
        $recipients->each(fn (Model $recipient) => $recipient->notify(app($notificationClass, [
            'conversationId' => $conversation->id,
            'recipientId' => $recipient->id,
        ])));
    }

    protected function postCreateHook(Conversation $conversation): void
    {
        $recipients = $conversation
            ->load('members')
            ->members
            ->filter(fn ($member) => $member->id !== (int) Auth::id());

        $this->sendCreatedNotifications($conversation, $recipients);
    }

    protected function postUpdateHook(Conversation $conversation): void
    {
        $conversation->load('members');

        $this->sendUpdatedNotifications($conversation, $conversation->members);
    }

    protected function postDeleteHook(Conversation $conversation): void
    {
        $recipients = $conversation->members->filter(fn ($member) => $member->id !== (int) Auth::id());

        $this->sendDeletedNotifications($conversation, $recipients);
    }
}
