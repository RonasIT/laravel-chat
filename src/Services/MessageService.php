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
            $message = $this->repository
                ->with([
                    'sender',
                    'conversation.members',
                ])
                ->create([
                    'conversation_id' => $data['conversation_id'],
                    'sender_id' => Auth::id(),
                    'text' => $data['text'],
                    'attachment_id' => Arr::get($data, 'attachment_id'),
                ]);

            $this->conversationService->update($data['conversation_id'], ['last_updated_at' => Carbon::now()]);

            return $message;
        });

        $recipients = $message->conversation->members->reject(fn ($member) => $member->id === $member->sender_id);

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

    public function markAsRead(int $lastReadMessageId): void
    {
        $message = $this->with('conversation')->find($lastReadMessageId);

        $this->repository->markAsRead($message, Auth::id());
    }
}
