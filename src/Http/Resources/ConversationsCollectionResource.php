<?php

namespace RonasIT\Chat\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use RonasIT\Chat\Contracts\Resources\ConversationResourceContract;

class ConversationsCollectionResource extends ResourceCollection
{
    public function collects(): string
    {
        return app()->getAlias(ConversationResourceContract::class);
    }
}
