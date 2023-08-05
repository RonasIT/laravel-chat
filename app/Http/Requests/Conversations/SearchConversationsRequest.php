<?php

namespace App\Http\Requests\Conversations;

use App\Contracts\Requests\SearchConversationsRequestContract;
use RonasIT\Support\BaseRequest;

class SearchConversationsRequest extends BaseRequest implements SearchConversationsRequestContract
{
    public function rules(): array
    {
        return [
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'string',
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
