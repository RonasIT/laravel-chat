<?php

namespace App\Http\Requests\Conversations;

use App\Contracts\Requests\GetConversationByUserIdRequestContract;
use App\Contracts\Services\ConversationServiceContract;

class GetConversationByUserIdRequest extends GetConversationRequest implements GetConversationByUserIdRequestContract
{
    protected function init(): void
    {
        $this->conversation = app(ConversationServiceContract::class)
            ->getConversationBetweenUsers($this->route('userId'), $this->user()->id);
    }
}
