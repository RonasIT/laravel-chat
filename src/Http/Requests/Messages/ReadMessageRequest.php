<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Requests\ReadMessageRequestContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Support\BaseRequest;
use RonasIT\Chat\Models\Message;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ReadMessageRequest extends BaseRequest implements ReadMessageRequestContract
{
    protected ?Message $message;

    public function validateResolved()
    {
        $this->init();

        parent::validateResolved();

        $this->checkMessageExists();

        $this->checkMessageRecipient();
    }

    protected function init()
    {
        $this->message = app(MessageServiceContract::class)->find($this->route('id'));
    }

    protected function checkMessageExists()
    {
        if (empty($this->message)) {
            throw new NotFoundHttpException(__('chat::validation.exceptions.not_found', ['entity' => 'Message']));
        }
    }

    protected function checkMessageRecipient()
    {
        if ($this->user()->id !== $this->message['recipient_id']) {
            throw new AccessDeniedHttpException(__('chat::validation.exceptions.not_message_recipient'));
        }
    }
}
