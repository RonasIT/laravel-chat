<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Requests\SearchMessagesRequestContract;
use RonasIT\Support\Http\BaseRequest;

class SearchMessagesRequest extends BaseRequest implements SearchMessagesRequestContract
{
    public function rules(): array
    {
        $availableOrderBy = implode(',', config('chat.order_by.message', []));

        return [
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'nullable|string',
            'order_by' => "string|in:{$availableOrderBy}",
            'desc' => 'boolean',
            'conversation_id' => 'integer',
            'with' => 'array',
            'with.*' => 'string|required|in:' . $this->getAvailableRelations(),
        ];
    }

    protected function getAvailableRelations(): string
    {
        return implode(',', [
            'conversation',
            'sender',
            'attachment',
        ]);
    }
}
