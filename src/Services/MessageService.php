<?php

namespace RonasIT\Chat\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use RonasIT\Chat\Contracts\Notifications\NewMessageNotificationContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Repositories\MessageRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @mixin MessageRepository
 *
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
        $message = DB::transaction(function () use ($data) {
            $conversation = $this->conversationService->getOrCreateConversationBetweenUsers(Auth::id(), $data['recipient_id']);

            $message = $this->repository
                ->with(['recipient', 'sender'])
                ->create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => Auth::id(),
                    'recipient_id' => $data['recipient_id'],
                    'text' => $data['text'],
                    'attachment_id' => Arr::get($data, 'attachment_id'),
                ]);

            $this->conversationService->update($conversation->id, ['last_updated_at' => Carbon::now()]);

            return $message;
        });

        $this->notifyUser($message, collect([$message->recipient]));

        return $message;
    }

    public function search(array $filters = []): LengthAwarePaginator
    {
        if (Auth::check()) {
            $filters['owner_id'] = Auth::id();
        }

        return $this
            ->searchQuery($filters)
            ->filterByOwner()
            ->getSearchResults();
    }

    public function notifyUser(Model $message, Collection $recipients): void
    {
        $newMessageNotification = app(NewMessageNotificationContract::class)->setMessage($message);

        Notification::send($recipients, $newMessageNotification);
    }

    public function markAsReadMessages($fromMessageId): int
    {
        return $this->repository->markAsReadMessages(Auth::id(), $fromMessageId);
    }
}
