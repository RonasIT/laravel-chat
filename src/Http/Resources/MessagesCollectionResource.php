<?php

namespace RonasIT\Chat\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use RonasIT\Chat\Contracts\Resources\MessageResourceContract;

class MessagesCollectionResource extends ResourceCollection
{
    public function collects(): string
    {
        return app()->getAlias(MessageResourceContract::class);
    }
}
