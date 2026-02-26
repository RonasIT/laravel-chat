<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use RonasIT\Chat\Contracts\Requests\GetConversationRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Models\Conversation;

class GetConversationRequest extends BaseConversationRequest implements GetConversationRequestContract
{
    protected ?Conversation $conversation;

    public function authorize(): bool
    {
        return $this->conversation->isMember($this->user());
    }

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
            'members',
            'last_message',
            'cover',
        ]);
    }

    public function validateResolved(): void
    {
        $this->init();

        $this->checkConversationExists();

        parent::validateResolved();
    }

    protected function init(): void
    {
        $this->conversation = app(ConversationServiceContract::class)->find($this->route('id'));
    }
}
