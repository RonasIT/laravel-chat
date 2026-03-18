<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Models\Message;
use RonasIT\Support\Http\BaseRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseMessageRequest extends BaseRequest
{
    protected ?Message $message;

    public function authorize(): bool
    {
        return $this
            ->message
            ->conversation
            ->hasMember($this->user());
    }

    protected function checkMessageExists(): void
    {
        if (empty($this->message)) {
            throw new NotFoundHttpException(__('chat::validation.exceptions.not_found', ['entity' => 'Message']));
        }
    }

    protected function init(): void
    {
        $this->message = app(MessageServiceContract::class)
            ->with('conversation')
            ->find($this->route('id'));
    }
}
