<?php

namespace RonasIT\Chat\Repositories;

use RonasIT\Chat\Models\Message;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property Message $model
 */
class MessageRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Message::class);

        $this->setAdditionalReservedFilters('member_id');
    }
}
