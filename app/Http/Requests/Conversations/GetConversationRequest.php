<?php

namespace App\Http\Requests\Conversations;

use App\Contracts\Requests\GetConversationRequestContract;
use App\Contracts\Services\ConversationServiceContract;
use App\Models\Conversation;
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
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Conversation']));
        }
    }

    protected function checkConversationOwnership(): void
    {
        $conversationOwnersIdsCollection = collect([$this->conversation['sender_id'], $this->conversation['recipient_id']]);

        if (!$conversationOwnersIdsCollection->contains($this->user()->id)) {
            throw new AccessDeniedHttpException(__('validation.exceptions.not_owner', ['entity' => 'Conversation']));
        }
    }
}
