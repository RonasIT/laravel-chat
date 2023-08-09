<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use RonasIT\Support\BaseRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseConversationRequest extends BaseRequest
{
    protected function checkConversationOwnership(): void
    {
        $conversationOwnersIdsCollection = collect([$this->conversation['sender_id'], $this->conversation['recipient_id']]);

        if (!$conversationOwnersIdsCollection->contains($this->user()->id)) {
            throw new AccessDeniedHttpException(__('chat::validation.exceptions.not_owner', ['entity' => 'Conversation']));
        }
    }

    protected function checkConversationExists(): void
    {
        if (empty($this->conversation)) {
            throw new NotFoundHttpException(__('chat::validation.exceptions.not_found', ['entity' => 'Conversation']));
        }
    }
}
