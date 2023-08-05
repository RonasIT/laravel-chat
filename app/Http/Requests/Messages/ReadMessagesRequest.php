<?php

namespace App\Http\Requests\Messages;

use App\Contracts\Requests\ReadMessagesRequestContract;
use App\Contracts\Services\MessageServiceContract;
use App\Models\Message;
use RonasIT\Support\BaseRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ReadMessagesRequest extends BaseRequest implements ReadMessagesRequestContract
{
    protected ?Message $message;

    public function validateResolved(): void
    {
        $this->init();

        parent::validateResolved();

        $this->checkMessageExists();

        $this->checkMessageRecipient();
    }

    protected function init(): void
    {
        $this->message = app(MessageServiceContract::class)->find($this->route('id'));
    }

    protected function checkMessageExists(): void
    {
        if (empty($this->message)) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Message']));
        }
    }

    protected function checkMessageRecipient(): void
    {
        if ($this->user()->id !== $this->message['recipient_id']) {
            throw new AccessDeniedHttpException(__('validation.exceptions.not_message_recipient'));
        }
    }
}
