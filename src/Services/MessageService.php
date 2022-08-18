<?php

namespace RonasIT\Chat\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use RonasIT\Chat\Contracts\Notifications\NewMessageNotificationContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Repositories\MessageRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use RonasIT\Support\Services\EntityService;

/**
 * @mixin MessageRepository
 * @property MessageRepository $repository
 */
class MessageService extends EntityService implements MessageServiceContract
{
    protected ConversationService $conversationService;

    public function __construct()
    {
        $this->setRepository(MessageRepository::class);

        $this->conversationService = app(ConversationServiceContract::class);
    }

    public function create(array $data): Model
    {
        $conversation = $this->conversationService->getOrCreateConversationBetweenUsers(Auth::id(), $data['recipient_id']);

        $message = $this
            ->with(['recipient', 'sender'])
            ->create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id(),
                'recipient_id' => $data['recipient_id'],
                'text' => $data['text'],
                'attachment_id' => Arr::get($data, 'attachment_id'),
            ]);

        $this->conversationService->update($conversation->id, ['last_updated_at' => Carbon::now()]);

        $this->notifyUser($message, collect([$message->recipient]));

        return $message;
    }

    public function search(array $filters = []): LengthAwarePaginator
    {
        if (Auth::id()) {
            $filters['owner_id'] = Auth::id();
        }

        return $this
            ->with(Arr::get($filters, 'with', []))
            ->searchQuery($filters)
            ->filterBy('conversation_id')
            ->filterByOwner()
            ->filterFrom('id', false, 'id_from')
            ->filterTo('id', false, 'id_to')
            ->filterFrom('created_at', false, 'created_at_from')
            ->filterTo('created_at', false, 'created_at_to')
            ->getSearchResults();
    }

    public function notifyUser(Model $message, Collection $recipients): void
    {
        $newMessageNotification = app(NewMessageNotificationContract::class)->setMessage($message);

        Notification::send($recipients, $newMessageNotification);
    }

    public function markAsReadMessages($id): int
    {
        return $this->repository->markAsReadMessages(Auth::id(), $id);
    }

    function find(int $id): ?Model
    {
        return $this->repository->find($id);
    }
}
