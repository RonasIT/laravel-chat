<?php

namespace RonasIT\Chat\Contracts\Resources;

use Illuminate\Http\Request;

interface ConversationResourceContract
{
    public function toArray(Request $request): array;
}
