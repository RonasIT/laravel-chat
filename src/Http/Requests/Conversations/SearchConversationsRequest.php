<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use RonasIT\Chat\Contracts\Models\ConversationModelContract;
use RonasIT\Chat\Contracts\Requests\SearchConversationsRequestContract;
use RonasIT\Chat\Enums\Conversation\TypeEnum;

class SearchConversationsRequest extends BaseConversationRequest implements SearchConversationsRequestContract
{
    public function rules(): array
    {
        return [
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'nullable|string',
            'order_by' => 'string|not_in:' . $this->getNotOrderableFields() . '|in:' . $this->getOrderableFields(app()->getAlias(ConversationModelContract::class)),
            'desc' => 'boolean',
            'type' => 'string|in:' . TypeEnum::toString(),
            'with_unread_messages_count' => 'boolean',
            'with' => 'array',
            'with.*' => 'string|required|in:' . $this->getAvailableRelations(),
            'with_count' => 'array',
            'with_count.*' => 'string|required|in:' . $this->getCountableRelations(),
        ];
    }

    protected function getNotOrderableFields(): string
    {
        return ($this->input('type') === TypeEnum::Group->value) ? 'cover_id' : 'title,cover_id';
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
}
