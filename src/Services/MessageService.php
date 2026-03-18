<?php

namespace RonasIT\Chat\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RonasIT\Chat\Contracts\Notifications\MessageCreatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\MessageUpdatedNotificationContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Models\Message;
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

            $conversation = $this->conversationService
                ->with('members')
                ->update($conversation->id, ['last_updated_at' => Carbon::now()]);

            return [$message, $conversation];
        });

        $recipients = $conversation->members->filter(fn ($member) => $member->id !== $message->sender_id);

        $this->notifyUser($message, $recipients);

        return $message;
    }

    public function search(array $filters = []): LengthAwarePaginator
    {
        if (Auth::check()) {
            $filters['member_id'] = Auth::id();
        }

        return $this
            ->withConversationOverridenTitleAndCover(Arr::get($filters, 'member_id'))
            ->searchQuery($filters)
            ->filterBy('conversation.members.member_id', 'member_id')
            ->getSearchResults();
    }

    public function notifyUser(Model $message, Collection $recipients): void
    {
        foreach ($recipients as $recipient) {
            $recipient->notify(
                app(MessageCreatedNotificationContract::class)
                    ->setMessage($message)
                    ->setRecipientId($recipient->id),
            );
        }
    }

    public function pin(int $id): void
    {
        $message = $this->with('conversation')->find($id);

        $this->conversationService->pinMessage($message->conversation, $message->id);
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

        $this->postUpdateHook($lastReadMessage);
    }

    protected function sendUpdatedNotifications(Message $message, Collection $recipients): void
    {
        $this->sendNotifications($message, $recipients, MessageUpdatedNotificationContract::class);
    }

    protected function sendNotifications(Message $message, Collection $recipients, string $notificationClass): void
    {
        $recipients->each(fn (Model $recipient) => $recipient->notify(app($notificationClass, [
            'message' => $message,
            'recipientId' => $recipient->id,
        ])));
    }

    protected function postUpdateHook(Message $message): void
    {
        $message->load('conversation.members');

        $this->sendUpdatedNotifications($message, $message->conversation->members);
    }
}
