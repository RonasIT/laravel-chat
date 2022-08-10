<?php

namespace RonasIT\Chat\Contracts\Services;

interface ConversationServiceContract
{
    public function search($data);

    public function get($id, $data);
}
