<?php

namespace RonasIT\Chat\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use RonasIT\Chat\Contracts\Requests\DeleteConversationRequestContract;
use RonasIT\Chat\Contracts\Requests\GetConversationRequestContract;
use RonasIT\Chat\Contracts\Requests\GetOrCreatePrivateConversationRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchConversationsRequestContract;
use RonasIT\Chat\Contracts\Requests\UpdateConversationRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use Symfony\Component\HttpFoundation\Response;

class ConversationController extends Controller
{
    public function get(GetConversationRequestContract $request, ConversationServiceContract $service, int $id): JsonResponse
    {
        $result = $service
            ->with($request->input('with', []))
            ->find($id);

        return response()->json($result);
    }

    public function search(SearchConversationsRequestContract $request, ConversationServiceContract $service): JsonResponse
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }

    public function getOrCreatePrivate(GetOrCreatePrivateConversationRequestContract $request, ConversationServiceContract $service): JsonResponse
    {
        $conversation = $service->getOrCreatePrivate($request->user()->id, $request->input('participant_id'));

        $result = $service
            ->with($request->input('with', []))
            ->find($conversation->id);

        return response()->json($result);
    }

    public function update(UpdateConversationRequestContract $request, ConversationServiceContract $service, int $id): Response
    {
        $service->update($id, $request->onlyValidated());

        return response()->noContent();
    }

    public function delete(DeleteConversationRequestContract $request, ConversationServiceContract $service, int $id): Response
    {
        $service->delete($id);

        return response('', Response::HTTP_NO_CONTENT);
    }
}
