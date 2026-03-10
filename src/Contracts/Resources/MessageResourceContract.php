<?php

namespace RonasIT\Chat\Contracts\Resources;

use Illuminate\Http\Request;

interface MessageResourceContract
{
    public function toArray(Request $request): array;
}
