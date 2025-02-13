<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Requests\ReadMessagesRequestContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Support\Http\BaseRequest;
use RonasIT\Chat\Models\Message;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ReadMessagesRequest extends BaseRequest implements ReadMessagesRequestContract
{
    protected ?Message $fromMessage;

    public function validateResolved(): void
    {
        $this->init();

        parent::validateResolved();

        $this->checkMessageExists();

        $this->checkMessageRecipient();
    }

    protected function init(): void
    {
        $this->fromMessage = app(MessageServiceContract::class)->find($this->route('id'));
    }

    protected function checkMessageExists(): void
    {
        if (empty($this->fromMessage)) {
            throw new NotFoundHttpException(__('chat::validation.exceptions.not_found', ['entity' => 'Message']));
        }
    }

    protected function checkMessageRecipient(): void
    {
        if ($this->user()->id !== $this->fromMessage['recipient_id']) {
            throw new AccessDeniedHttpException(__('chat::validation.exceptions.not_message_recipient'));
        }
    }
}
