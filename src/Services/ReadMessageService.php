<?php

namespace RonasIT\Chat\Services;

use RonasIT\Chat\Repositories\ReadMessageRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @mixin ReadMessageRepository
 *
 * @property ReadMessageRepository $repository
 */
class ReadMessageService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(ReadMessageRepository::class);
    }
}
