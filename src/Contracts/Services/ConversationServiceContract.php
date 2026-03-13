<?php

namespace RonasIT\Chat\Contracts\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use RonasIT\Chat\Models\Conversation;

interface ConversationServiceContract
{
    public function search(array $filters = []): LengthAwarePaginator;

    public function getOrCreatePrivate(int $firstMemberId, int $secondMemberId): Model;

    public function getPrivate(int $firstMemberId, int $secondMemberId): ?Model;

    public function delete($where): void;

    public function notifyUser($conversation, $recipients): void;

    public function sendCreatedNotifications(Conversation $conversation, Collection $recipients): void;

    public function sendUpdatedNotifications(Conversation $conversation, Collection $recipients): void;
}
