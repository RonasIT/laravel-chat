<?php

namespace RonasIT\Chat\Contracts\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface ConversationServiceContract
{
    public function search(array $filters = []): LengthAwarePaginator;

    public function getOrCreatePrivate(int $firstMemberId, int $secondMemberId): Model;

    public function getPrivate(int $asUserId, int $withUserId): ?Model;

    public function delete($where): void;

    public function with(array|string $relations): static;

    public function withCount(array|string $relations): static;

    public function withCalculatedIdentity(int $memberId): static;
}
