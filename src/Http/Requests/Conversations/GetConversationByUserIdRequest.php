<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use RonasIT\Chat\Contracts\Requests\GetConversationByUserIdRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Models\Conversation;

class GetConversationByUserIdRequest extends BaseConversationRequest implements GetConversationByUserIdRequestContract
{
    protected ?Conversation $conversation;

    protected function init(): void
    {
        $userId = $this->route('userId');
        $authId = $this->user()->id;

        $this->conversation = app(ConversationServiceContract::class)
            ->getConversationBetweenUsers($userId, $authId);
    }
}
