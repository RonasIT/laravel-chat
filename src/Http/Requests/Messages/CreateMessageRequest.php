<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Requests\CreateMessageRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Support\Http\BaseRequest;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreateMessageRequest extends BaseRequest implements CreateMessageRequestContract
{
    public function rules(): array
    {
        $mediaTableName = app(config('chat.classes.media_model'))->getTable();
        $userTableName = app(config('chat.classes.user_model'))->getTable();

        return [
            'recipient_id' => "required_without:conversation_id|prohibits:conversation_id|integer|exists:{$userTableName},id",
            'conversation_id' => 'required_without:recipient_id|integer|exists:conversations,id',
            'text' => 'string|required',
            'attachment_id' => "integer|exists:{$mediaTableName},id",
        ];
    }

    public function validateResolved(): void
    {
        parent::validateResolved();

        ($this->has('recipient_id')) ? $this->checkSelfMessage() : $this->checkConversationMembership();
    }

    protected function checkSelfMessage(): void
    {
        if ($this->user()->id === $this->input('recipient_id')) {
            throw new BadRequestHttpException(__('chat::validation.exceptions.self_message'));
        }
    }

    protected function checkConversationMembership(): void
    {
        $conversation = app(ConversationServiceContract::class)->find($this->input('conversation_id'));

        if (!$conversation->isMember($this->user())) {
            throw new AccessDeniedHttpException(__('chat::validation.exceptions.not_conversation_member'));
        }
    }
}
