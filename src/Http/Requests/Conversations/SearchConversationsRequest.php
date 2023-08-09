<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use RonasIT\Chat\Contracts\Requests\SearchConversationsRequestContract;

class SearchConversationsRequest extends BaseConversationRequest implements SearchConversationsRequestContract
{
    public function rules(): array
    {
        return [
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'nullable|string',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with_unread_messages_count' => 'boolean',
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
}
