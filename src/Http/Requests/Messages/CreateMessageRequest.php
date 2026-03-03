<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Requests\CreateMessageRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Support\Http\BaseRequest;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateMessageRequest extends BaseRequest implements CreateMessageRequestContract
{
    protected ?Conversation $conversation;

    public function rules(): array
    {
        $mediaTableName = app(config('chat.classes.media_model'))->getTable();
        $userTableName = app(config('chat.classes.user_model'))->getTable();

        return [
            'recipient_id' => "required_without:conversation_id|prohibits:conversation_id|integer|exists:{$userTableName},id",
            'conversation_id' => 'required_without:recipient_id|prohibits:recipient_id|integer',
            'text' => 'string|required',
            'attachment_id' => "integer|exists:{$mediaTableName},id",
        ];
    }

    public function validateResolved(): void
    {
        parent::validateResolved();

        $this->init();

        if ($this->has('conversation_id')) {
            $this->checkConversationExists();
            $this->checkConversationMembership();
        }

        $this->checkSelfMessage();
    }

    protected function checkSelfMessage(): void
    {
        if ($this->has('recipient_id') && $this->user()->id === $this->input('recipient_id')) {
            throw new BadRequestHttpException(__('chat::validation.exceptions.self_message'));
        }
    }

    protected function checkConversationMembership(): void
    {
        if (!$this->conversation->hasMember($this->user())) {
            throw new AccessDeniedHttpException(__('chat::validation.exceptions.not_conversation_member'));
        }
    }

    protected function checkConversationExists(): void
    {
        if (is_null($this->conversation)) {
            throw new NotFoundHttpException(__('chat::validation.exceptions.not_found', ['entity' => 'Conversation']));
        }
    }

    protected function init(): void
    {
        $this->conversation = app(ConversationServiceContract::class)->find($this->input('conversation_id'));
    }
}
