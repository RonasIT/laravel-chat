<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use RonasIT\Support\Http\BaseRequest;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseConversationRequest extends BaseRequest
{
    protected function checkConversationMembership(): void
    {
        if (!$this->conversation->hasMember($this->user())) {
            throw new AccessDeniedHttpException(__('chat::validation.exceptions.not_conversation_member'));
        }
    }

    protected function checkConversationCreatorship(): void
    {
        if (!$this->conversation->isCreator($this->user())) {
            throw new AccessDeniedHttpException(__('chat::validation.exceptions.not_creator'));
        }
    }

    protected function checkConversationExists(): void
    {
        if (empty($this->conversation)) {
            throw new NotFoundHttpException(__('chat::validation.exceptions.not_found', ['entity' => 'Conversation']));
        }
    }
}
