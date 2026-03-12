<?php

namespace RonasIT\Chat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RonasIT\Chat\Contracts\Notifications\ConversationUpdatedNotificationContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;

class SendConversationUpdatedNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected readonly int $conversationId,
        protected readonly Model $recipient,
    ) {
    }

    public function handle(ConversationServiceContract $conversationService): void
    {
        $conversation = $conversationService
            ->withUnreadCountMemberId($this->recipient->id)
            ->find($this->conversationId);

        $this->recipient->notify(app(ConversationUpdatedNotificationContract::class, [
            'conversation' => $conversation,
            'recipientId' => $this->recipient->id,
        ]));
    }
}
