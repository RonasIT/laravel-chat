<?php

namespace RonasIT\Chat\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use RonasIT\Chat\Contracts\Notifications\ConversationCreatedNotificationContractContract;
use RonasIT\Chat\Contracts\Notifications\ConversationDeletedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\ConversationUpdatedNotificationContractContract;
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
        foreach ($recipients as $recipient) {
            $recipient->notify(
                app(ConversationDeletedNotificationContract::class)
                    ->setConversation($conversation)
                    ->setRecipientId($recipient->id),
            );
        }
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
            ->withUnreadCountMemberId($forMemberId)
            ->searchQuery($filters)
            ->filterBy('members.member_id', 'member_id')
            ->getSearchResults();
    }

    public function getPrivate(int $firstMemberId, int $secondMemberId): ?Model
    {
        return $this->getByTypeAndMembers(TypeEnum::Private, $firstMemberId, $secondMemberId);
    }

    public function sendCreatedNotifications(Conversation $conversation, Collection $recipients): void
    {
        $this->sendNotifications($conversation, $recipients, ConversationCreatedNotificationContractContract::class);
    }

    public function sendUpdatedNotifications(Conversation $conversation, Collection $recipients): void
    {
        $this->sendNotifications($conversation, $recipients, ConversationUpdatedNotificationContractContract::class);
    }

    protected function sendNotifications(Conversation $conversation, Collection $recipient, string $notificationClass): void
    {
        $recipient->each(fn (Model $recipient) => $recipient->notify(app($notificationClass, [
            'conversation' => $conversation,
            'recipientId' => $recipient->id,
        ])));
    }
}
