<?php

namespace RonasIT\Chat\Contracts\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface MessageModelContract
{
    public function conversation(): BelongsTo;

    public function sender(): BelongsTo;

    public function attachment(): BelongsTo;

    public function reads(): HasMany;

    public function scopeWithIsRead(Builder $query): Builder;

    public function getIsReadAttribute();
}
