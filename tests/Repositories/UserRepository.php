<?php

namespace RonasIT\Chat\Tests\Repositories;

use RonasIT\Chat\Tests\Models\User;
use RonasIT\Support\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(User::class);
    }
}
