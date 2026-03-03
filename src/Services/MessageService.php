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
        list($message, $conversation) = DB::transaction(function () use ($data) {
            $conversation = (Arr::has($data, 'recipient_id'))
                ? $this->conversationService->getOrCreatePrivate(Auth::id(), $data['recipient_id'])
                : $this->conversationService->find($data['conversation_id']);

            $message = $this->repository
                ->with('sender')
                ->create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => Auth::id(),
                    'text' => $data['text'],
                    'attachment_id' => Arr::get($data, 'attachment_id'),
                ]);

            $conversation = $this->conversationService
                ->with('members')
                ->update($conversation->id, ['last_updated_at' => Carbon::now()]);

            return [$message, $conversation];
        });

        $recipients = $conversation->members->filter(fn ($member) => $member->id !== Auth::id());

        $this->notifyUser($message, $recipients);

        return $message;
    }

    public function search(array $filters = []): LengthAwarePaginator
    {
        if (Auth::check()) {
            $filters['member_id'] = Auth::id();
        }

        return $this
            ->searchQuery($filters)
            ->filterBy('conversation.members.member_id', 'member_id')
            ->getSearchResults();
    }

    public function notifyUser(Model $message, Collection $recipients): void
    {
        $newMessageNotification = app(NewMessageNotificationContract::class)->setMessage($message);

        Notification::send($recipients, $newMessageNotification);
    }
}
