<?php

namespace RonasIT\Chat\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use RonasIT\Chat\Contracts\Resources\MessagesCollectionResourceContract;

class MessagesCollectionResource extends ResourceCollection implements MessagesCollectionResourceContract
{
    public $collects = MessageResource::class;
}
