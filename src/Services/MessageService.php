<?php

namespace RonasIT\Chat\Services;

use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Models\Message;
use RonasIT\Chat\Notifications\NewMessageNotification;
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

    public function create(array $data): Message
    {
        $conversation = $this->conversationService->getOrCreateConversationBetweenUsers(Auth::user()->id, $data['recipient_id']);

        $message = $this->repository
            ->with(['recipient', 'sender'])
            ->create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::user()->id,
                'recipient_id' => $data['recipient_id'],
                'text' => $data['text'],
                'attachment_id' => Arr::get($data, 'attachment_id'),
            ]);

        $this->conversationService->update($conversation->id, ['last_updated_at' => Carbon::now()]);

        $this->notifyUser($message, collect([$message->recipient]));

        return $message;
    }

    public function search($filters)
    {
        if (!empty(Auth::user())) {
            $filters['owner_id'] = Auth::user()->id;
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
        $newMessageNotification = new NewMessageNotification($message, Auth::user());

        Notification::send($recipients, $newMessageNotification);
    }

    public function markAsReadMessages($id)
    {
        return $this->repository->markAsReadMessages(Auth::user()->id, $id);
    }
}
