<?php

namespace RonasIT\Chat\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use RonasIT\Chat\Contracts\Resources\ConversationCollectionResourceContract;

class ConversationCollectionResource extends ResourceCollection implements ConversationCollectionResourceContract
{
    public $collects = ConversationResource::class;
}
