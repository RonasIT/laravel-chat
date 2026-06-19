<?php

namespace RonasIT\Chat\Repositories;

use RonasIT\Chat\Models\ReadMessage;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property ReadMessage $model
 */
class ReadMessageRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(ReadMessage::class);
    }
}
