<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Support\BaseRequest;
use RonasIT\Chat\Contracts\Requests\SearchMessagesRequestContract;

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
            'with.*' => "string|required",
        ];
    }

}
