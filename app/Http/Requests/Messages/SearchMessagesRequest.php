<?php

namespace App\Http\Requests\Messages;

use App\Contracts\Requests\SearchMessagesRequestContract;
use RonasIT\Support\BaseRequest;

class SearchMessagesRequest extends BaseRequest implements SearchMessagesRequestContract
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
            'conversation_id' => 'integer',
            'with' => 'array',
            'with.*' => 'string|required|in:' . $this->getAvailableRelations(),
        ];
    }

    protected function getAvailableRelations(): string
    {
        return join(',', [
            'conversation',
            'sender',
            'recipient',
            'attachment',
        ]);
    }
}
