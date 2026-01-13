<?php

namespace RonasIT\Chat\Contracts\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface MessageServiceContract
{
    public function create(array $data): Model;

    public function search(array $filters = []): LengthAwarePaginator;

    public function notifyUser(Model $message, Collection $recipients): void;

    public function markAsReadMessages(int $fromMessageId): int;
}
