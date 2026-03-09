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
        return $this->conversation->hasMember($this->user());
    }

    public function rules(): array
    {
        return [
            'with' => 'array',
            'with.*' => 'string|required|in:' . $this->getAvailableRelations(),
            'with_count' => 'array',
            'with_count.*' => 'string|required|in:' . $this->getCountableRelations(),
        ];
    }

    protected function getAvailableRelations(): string
    {
        return implode(',', [
            'messages',
            'creator',
            'members',
            'last_message',
            'cover',
            'pinned_messages',
        ]);
    }

    protected function getCountableRelations(): string
    {
        return implode(',', [
            'members',
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
