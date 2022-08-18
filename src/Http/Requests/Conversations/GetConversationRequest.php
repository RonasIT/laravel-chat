<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use RonasIT\Chat\Contracts\Requests\GetConversationRequestContract;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Chat\Services\ConversationService;
use RonasIT\Support\BaseRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GetConversationRequest extends BaseRequest implements GetConversationRequestContract
{
    protected ?Conversation $conversation;

    public function rules(): array
    {
        return [
            'with' => 'array',
            'with.*' => 'string|required|in:' . $this->getAvailableRelations(),
        ];
    }

    protected function getAvailableRelations(): string
    {
        return join(',', [
            'messages',
            'sender',
            'recipient',
            'last_message',
        ]);
    }

    public function validateResolved()
    {
        $this->init();

        parent::validateResolved();

        $this->checkConversationExists();

        $this->checkConversationOwnership();
    }

    protected function init()
    {
        $this->conversation = app(ConversationService::class)->find($this->route('id'));
    }

    protected function checkConversationExists()
    {
        if (empty($this->conversation)) {
            throw new NotFoundHttpException(__('chat::validation.exceptions.not_found', ['entity' => 'Conversation']));
        }
    }

    protected function checkConversationOwnership()
    {
        $conversationOwnersIdsCollection = collect([$this->conversation['sender_id'], $this->conversation['recipient_id']]);

        if (!$conversationOwnersIdsCollection->contains($this->user()->id)) {
            throw new AccessDeniedHttpException(__('chat::validation.exceptions.not_owner', ['entity' => 'Conversation']));
        }
    }
}
