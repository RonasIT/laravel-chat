<?php

namespace RonasIT\Chat\Contracts\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use RonasIT\Chat\Models\Conversation;

interface MessageServiceContract
{
    public function create(array $data): Model;

    public function search(array $filters = []): LengthAwarePaginator;

    public function notifyUser(Conversation $conversation, Model $message, Collection $recipients): void;

    public function read(int $toID): void;

    public function pin(int $id): void;
}
