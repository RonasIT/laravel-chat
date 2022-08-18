<?php

namespace RonasIT\Chat\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use RonasIT\Chat\Contracts\Requests\GetConversationRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchConversationsRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;

class ConversationController extends Controller
{
    public function get(GetConversationRequestContract $request, ConversationServiceContract $service, $id): JsonResponse
    {
        $result = $service->find($id, $request->validated());

        return response()->json($result);
    }

    public function search(SearchConversationsRequestContract $request, ConversationServiceContract $service): JsonResponse
    {
        $result = $service->search($request->validated());

        return response()->json($result);
    }
}
