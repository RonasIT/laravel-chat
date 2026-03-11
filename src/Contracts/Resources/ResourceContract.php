<?php

namespace RonasIT\Chat\Contracts\Resources;

use Illuminate\Http\Request;

interface ResourceContract
{
    public function toArray(Request $request): array;
}
