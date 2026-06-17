<?php

namespace RonasIT\Chat\Http\Actions\Conversation;

use Illuminate\Pagination\LengthAwarePaginator;
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

        return $this->conversationService->search($filters);
    }
}
