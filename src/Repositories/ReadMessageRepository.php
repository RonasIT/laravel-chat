<?php

namespace RonasIT\Chat\Repositories;

use RonasIT\Chat\Models\ReadMessage;

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
