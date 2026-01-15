<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use RonasIT\Chat\Contracts\Requests\GetConversationRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Models\Conversation;

class GetConversationRequest extends BaseConversationRequest implements GetConversationRequestContract
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
        return implode(',', [
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
}
