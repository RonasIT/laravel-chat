<?php

namespace RonasIT\Chat\Contracts\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface MessageServiceContract
{
    function create(array $data);

    function search($filters);

    function notifyUser(Model $message, Collection $recipients);

    function markAsReadMessages(int $id);
}
