<?php

namespace RonasIT\Chat\Http\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;

readonly class SearchAction
{
    public function __construct(
        protected ConversationServiceContract $conversationService,
    ) {
    }

    public function execute(array $filters = []): LengthAwarePaginator
    {
        $filters['member_id'] = Auth::id();

        $filters['with_unread_messages_count_for_member_id'] = Arr::get($filters, 'with_unread_messages_count', false)
            ? Auth::id()
            : null;

        return $this->conversationService->search($filters);
    }
}
