<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Requests\CreateMessageRequestContract;
use RonasIT\Support\Http\BaseRequest;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreateMessageRequest extends BaseRequest implements CreateMessageRequestContract
{
    public function rules(): array
    {
        $mediaTableName = app(config('chat.classes.media_model'))->getTable();
        $userTableName = app(config('chat.classes.user_model'))->getTable();

        return [
            'recipient_id' => "required|integer|exists:{$userTableName},id",
            'text' => 'string|required',
            'attachment_id' => "integer|exists:{$mediaTableName},id",
        ];
    }

    public function validateResolved(): void
    {
        parent::validateResolved();

        $this->checkSelfMessage();
    }

    protected function checkSelfMessage(): void
    {
        if ($this->user()->id === $this->input('recipient_id')) {
            throw new BadRequestHttpException(__('chat::validation.exceptions.self_message'));
        }
    }
}
