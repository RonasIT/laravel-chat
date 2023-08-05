<?php

namespace App\Http\Controllers;

use App\Contracts\Requests\DeleteConversationRequestContract;
use App\Contracts\Requests\GetConversationByUserIdRequestContract;
use App\Contracts\Requests\GetConversationRequestContract;
use App\Contracts\Requests\SearchConversationsRequestContract;
use App\Contracts\Services\ConversationServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ConversationController extends Controller
{
    public function get(GetConversationRequestContract $request, ConversationServiceContract $service, $id): JsonResponse
    {
        $result = $service
            ->with($request->input('with', []))
            ->find($id);

        return response()->json($result);
    }

    public function getByUserId(GetConversationByUserIdRequestContract $request, ConversationServiceContract $service, $userId): JsonResponse
    {
        $result = $service
            ->with($request->input('with', []))
            ->getConversationBetweenUsers($request->user()->id, $userId);

        return response()->json($result);
    }

    public function search(SearchConversationsRequestContract $request, ConversationServiceContract $service): JsonResponse
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }

    public function delete(DeleteConversationRequestContract $request, ConversationServiceContract $service, $id): Response
    {
        $service->delete($id);

        return response('', Response::HTTP_NO_CONTENT);
    }
}
