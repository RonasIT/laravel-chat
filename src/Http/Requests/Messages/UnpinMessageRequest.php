<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Requests\UnpinMessageRequestContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Models\Message;

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

        parent::validateResolved();
    }

    protected function init(): void
    {
        $this->message = app(MessageServiceContract::class)
            ->with('conversation')
            ->find($this->route('id'));
    }
}
