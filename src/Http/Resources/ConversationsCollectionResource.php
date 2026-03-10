<?php

namespace RonasIT\Chat\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use RonasIT\Chat\Contracts\Resources\ConversationsCollectionResourceContract;

class ConversationsCollectionResource extends ResourceCollection implements ConversationsCollectionResourceContract
{
    public $collects = ConversationResource::class;
}
