<?php

namespace RonasIT\Chat\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use RonasIT\Chat\Contracts\Resources\MessageCollectionResourceContract;

class MessageCollectionResource extends ResourceCollection implements MessageCollectionResourceContract
{
    public $collects = MessageResource::class;
}
