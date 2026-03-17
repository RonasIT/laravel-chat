<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Requests\UnpinMessageRequestContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Models\Message;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class UnpinMessageRequest extends BaseMessageRequest implements UnpinMessageRequestContract
{
    protected ?Message $message;

    public function authorize(): bool
    {
        return $this
            ->message
            ->conversation
            ->hasMember($this->user());
    }

    public function validateResolved(): void
    {
        $this->init();

        $this->checkMessageExists();

        $this->checkMessagePinned();

        parent::validateResolved();
    }

    protected function init(): void
    {
        $this->message = app(MessageServiceContract::class)
            ->with('conversation.pinned_messages')
            ->find($this->route('id'));
    }

    protected function checkMessagePinned(): void
    {
        if (!$this->isMessagePinned()) {
            throw new ConflictHttpException(__('chat::validation.custom.message_not_pinned'));
        }
    }

    protected function isMessagePinned(): bool
    {
        return $this
            ->message
            ->conversation
            ->pinned_messages
            ->contains($this->message->id);
    }
}
