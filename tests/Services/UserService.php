<?php

namespace RonasIT\Chat\Tests\Services;

use Illuminate\Support\Collection;
use RonasIT\Chat\Contracts\Services\ConversationMemberServiceContract;
use RonasIT\Chat\Tests\Repositories\UserRepository;
use RonasIT\Support\Services\EntityService;

class UserService extends EntityService implements ConversationMemberServiceContract
{
    public function __construct()
    {
        $this->setRepository(UserRepository::class);
    }

    public function getByList(array $values, ?string $field = null): Collection
    {
        return $this->repository->getByList($values, $field);
    }
}
