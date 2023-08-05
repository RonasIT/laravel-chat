<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface ConversationServiceContract
{
    public function search(array $filters = []): LengthAwarePaginator;

    public function getOrCreateConversationBetweenUsers(int $senderId, int $recipientId): Model;

    public function delete($where): void;

    public function notifyUser($conversation, $recipients): void;
}
