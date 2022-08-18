<?php

namespace RonasIT\Chat\Contracts\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface MessageServiceContract
{
    function create(array $data): Model;

    function search(array $filters = []): LengthAwarePaginator;

    function notifyUser(Model $message, Collection $recipients);

    function markAsReadMessages(int $id);

    function find(int $id): ?Model;
}
