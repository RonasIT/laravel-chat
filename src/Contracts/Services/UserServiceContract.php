<?php

namespace RonasIT\Chat\Contracts\Services;

use Illuminate\Database\Eloquent\Model;

interface UserServiceContract
{
    function find(int $id): ?Model;
}
