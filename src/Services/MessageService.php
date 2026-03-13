<?php

namespace RonasIT\Chat\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RonasIT\Chat\Contracts\Notifications\NewMessageNotificationContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Repositories\MessageRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @mixin MessageRepository
 *
 * @property MessageRepository $repository
 */
class MessageService extends EntityService implements MessageServiceContract
{
    public function __construct(
        protected readonly ConversationServiceContract $conversationService,
        protected readonly ReadMessageService $readMessageService,
    ) {
        $this->setRepository(MessageRepository::class);
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

            $this->conversationService->update($conversation->id, ['last_updated_at' => Carbon::now()]);

            return [$message, $conversation];
        });

        $recipients = $conversation
            ->load('members')
            ->members
            ->filter(fn ($member) => $member->id !== $message->sender_id);

        $this->notifyUser($conversation, $message, $recipients);

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

    public function notifyUser(Conversation $conversation, Model $message, Collection $recipients): void
    {
        if ($conversation->wasRecentlyCreated) {
            $this->conversationService->sendCreatedNotifications($conversation, $recipients);
        }

        foreach ($recipients as $recipient) {
            $recipient->notify(
                app(NewMessageNotificationContract::class)
                    ->setMessage($message)
                    ->setRecipientId($recipient->id),
            );
        }
    }

    public function pin(int $id): void
    {
        $message = $this->with('conversation.members')->find($id);

        $result = $this->conversationService->pinMessage($message->conversation, $message->id);

        if (empty($result['attached'])) {
            return;
        }

        $this->conversationService->sendUpdatedNotifications($message->conversation, $message->conversation->members);
    }

    public function read(int $toID): void
    {
        $lastReadMessage = $this->find($toID);

        $unreadMessageIds = $this->getUnreadIdsByUser(
            conversationId: $lastReadMessage->conversation_id,
            toMessageId: $toID,
            memberId: Auth::id(),
        );

        if (empty($unreadMessageIds)) {
            return;
        }

        $this->readMessageService->insertOrIgnore(array_map(fn ($messageId) => [
            'message_id' => $messageId,
            'member_id' => Auth::id(),
        ], $unreadMessageIds));
    }
}
