<?php

namespace RonasIT\Chat\Contracts\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface ConversationServiceContract
{
    public function search(array $filters = []): LengthAwarePaginator;

    public function getOrCreatePrivate(int $firstMemberId, int $secondMemberId): Model;

    public function update(int $id, array $data): ?Model;

    public function delete($where): void;

    public function notifyUser($conversation, $recipients): void;
}
