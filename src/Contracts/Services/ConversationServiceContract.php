<?php

namespace RonasIT\Chat\Contracts\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface ConversationServiceContract
{
    public function search(array $filters = []): LengthAwarePaginator;

    public function getOrCreatePrivate(int $firstMemberId, int $secondMemberId): Model;

    public function getPrivate(int $firstMemberId, int $secondMemberId): ?Model;

    public function delete($where): void;

    public function retrieveById(int $id): ?Model;
}
