<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use App\Models\Conversation;
use RonasIT\Chat\Contracts\Requests\DeleteConversationRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use RonasIT\Support\BaseRequest;

class DeleteConversationRequest extends BaseRequest implements DeleteConversationRequestContract
{
    protected ?Conversation $conversation;

    public function rules(): array
    {
        return [];
    }

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

    protected function checkConversationOwnership(): void
    {
        $conversationOwnersIdsCollection = collect([$this->conversation['sender_id'], $this->conversation['recipient_id']]);

        if (!$conversationOwnersIdsCollection->contains($this->user()->id)) {
            throw new AccessDeniedHttpException(__('chat::validation.exceptions.not_owner', ['entity' => 'Conversation']));
        }
    }
}
