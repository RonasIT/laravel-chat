<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use RonasIT\Chat\Contracts\Requests\GetConversationByUserIdRequestContract;
use RonasIT\Support\Http\BaseRequest;

class GetConversationByUserIdRequest extends BaseRequest implements GetConversationByUserIdRequestContract
{
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
            'creator',
            'members',
            'last_message',
            'cover',
        ]);
    }
}
