<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use RonasIT\Chat\Contracts\Requests\GetConversationByUserIdRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;

class GetConversationByUserIdRequest extends GetConversationRequest implements GetConversationByUserIdRequestContract
{
    protected function init(): void
    {
        $this->conversation = app(ConversationServiceContract::class)
            ->getConversationBetweenUsers($this->route('userId'), $this->user()->id);
    }
}
