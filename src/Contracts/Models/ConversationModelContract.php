<?php

namespace RonasIT\Chat\Contracts\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

interface ConversationModelContract
{
    public function last_message(): HasOne;

    public function messages(): HasMany;

    public function creator(): BelongsTo;

    public function members(): BelongsToMany;

    public function cover(): BelongsTo;

    public function pinned_messages(): BelongsToMany;

    public function scopeWithUnreadMessagesCount(Builder $query, int $memberId): Builder;

    public function hasMember(Model $member): bool;

    public function isCreator(Model $member): bool;

    public function isGroup(): bool;

    public function isPrivate(): bool;
}
