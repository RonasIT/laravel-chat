<?php

namespace RonasIT\Chat\Contracts\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface MessageServiceContract
{
    public function create(array $data): Model;

    public function search(array $filters = []): LengthAwarePaginator;

    public function read(int $toID): void;

    public function pin(int $id): void;
}
