<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Support\Http\BaseRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseMessageRequest extends BaseRequest
{
    protected function checkMessageExists(): void
    {
        if (empty($this->message)) {
            throw new NotFoundHttpException(__('chat::validation.exceptions.not_found', ['entity' => 'Message']));
        }
    }
}
