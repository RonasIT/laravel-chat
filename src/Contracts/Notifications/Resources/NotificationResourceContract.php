<?php

namespace RonasIT\Chat\Contracts\Notifications\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\PotentiallyMissing;
use JsonSerializable;

interface NotificationResourceContract extends Arrayable, JsonSerializable, PotentiallyMissing
{
    public function toArray(): array;

    public function isMissing(): bool;
}