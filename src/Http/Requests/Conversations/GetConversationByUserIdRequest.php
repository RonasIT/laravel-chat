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
}
