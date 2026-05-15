<?php

namespace RonasIT\Chat\Http\Actions\Message;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;

readonly class SearchAction
{
    public function __construct(
        protected MessageServiceContract $messageService,
    ) {
    }

    public function execute(array $filters = []): LengthAwarePaginator
    {
        $filters['member_id'] = Auth::id();

        return $this->messageService->search($filters);
    }
}
