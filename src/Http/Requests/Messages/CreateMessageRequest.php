<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Services\UserServiceContract;
use RonasIT\Support\BaseRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use RonasIT\Chat\Contracts\Requests\CreateMessageRequestContract;

class CreateMessageRequest extends BaseRequest implements CreateMessageRequestContract
{
    protected $targetUser;

    public function rules(): array
    {
        $mediaTableName = config('chat.database.tables.media');

        return [
            'recipient_id' => 'required|integer|filled',
            'text' => 'string|required',
            'attachment_id' => "integer|exists:{$mediaTableName},id",
        ];
    }

    public function validateResolved()
    {
        $this->init();

        parent::validateResolved();

        $this->checkTargetUserExists();

        $this->checkSelfMessage();
    }

    protected function init()
    {
        $this->targetUser = app(UserServiceContract::class)->find($this->input('recipient_id'));
    }

    protected function checkTargetUserExists()
    {
        if (empty($this->targetUser)) {
            throw new NotFoundHttpException(__('chat::validation.exceptions.not_found', ['entity' => 'User']));
        }
    }

    protected function checkSelfMessage()
    {
        if ($this->user()->id === $this->input('recipient_id')) {
            throw new BadRequestHttpException(__('chat::validation.exceptions.self_message'));
        }
    }
}
