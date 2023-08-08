<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use RonasIT\Chat\Contracts\Requests\DeleteConversationRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Models\Conversation;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteConversationRequest extends BaseConversationRequest implements DeleteConversationRequestContract
{
    protected ?Conversation $conversation;

    public function validateResolved(): void
    {
        $this->init();

        parent::validateResolved();

        $this->checkConversationExists();

        $this->checkConversationOwnership();
    }

    protected function init(): void
    {
        $this->conversation = app(ConversationServiceContract::class)->find($this->route('id'));
    }

    protected function checkConversationExists(): void
    {
        if (empty($this->conversation)) {
            throw new NotFoundHttpException(__('chat::validation.exceptions.not_found', ['entity' => 'Conversation']));
        }
    }
}
