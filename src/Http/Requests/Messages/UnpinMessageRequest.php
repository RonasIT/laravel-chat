<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Requests\UnpinMessageRequestContract;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class UnpinMessageRequest extends BaseMessageRequest implements UnpinMessageRequestContract
{
    public function validateResolved(): void
    {
        $this->init();

        $this->checkMessageExists();

        $this->checkMessagePinned();

        parent::validateResolved();
    }

    protected function checkMessagePinned(): void
    {
        if (!$this->message->conversation->hasPinnedMessage($this->message->id)) {
            throw new ConflictHttpException(__('chat::validation.custom.message_not_pinned'));
        }
    }
}
