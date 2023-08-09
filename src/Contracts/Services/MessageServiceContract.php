<?php

namespace RonasIT\Chat\Contracts\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface MessageServiceContract
{
    function create(array $data): Model;

    function search(array $filters = []): LengthAwarePaginator;

    function notifyUser(Model $message, Collection $recipients): void;

    function markAsReadMessages(int $fromMessageId): int;
}
