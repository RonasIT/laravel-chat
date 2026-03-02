<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Requests\ReadMessageRequestContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Models\Message;
use RonasIT\Support\Http\BaseRequest;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReadMessageRequest extends BaseRequest implements ReadMessageRequestContract
{
    protected ?Message $message;

    public function validateResolved(): void
    {
        parent::validateResolved();

        $this->init();

        $this->checkMessageExists();

        $this->checkConversationMembership();
    }

    protected function checkMessageExists(): void
    {
        if (empty($this->message)) {
            throw new NotFoundHttpException(__('chat::validation.exceptions.not_found', ['entity' => 'Message']));
        }
    }

    protected function checkConversationMembership(): void
    {
        if (!$this->message->conversation->isMember($this->user())) {
            throw new AccessDeniedHttpException(__('chat::validation.exceptions.not_conversation_member'));
        }
    }

    protected function init(): void
    {
        $this->message = app(MessageServiceContract::class)->with('conversation')->find($this->route('id'));
    }
}
