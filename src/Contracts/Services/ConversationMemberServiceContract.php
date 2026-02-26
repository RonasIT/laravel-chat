<?php

namespace RonasIT\Chat\Contracts\Services;

use Illuminate\Support\Collection;

interface ConversationMemberServiceContract
{
    public function getByList(array $values, ?string $field = null): Collection;
}
