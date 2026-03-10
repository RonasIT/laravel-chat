<?php

namespace RonasIT\Chat\Http\Controllers;

use Illuminate\Routing\Controller;
use RonasIT\Chat\Contracts\Requests\DeleteConversationRequestContract;
use RonasIT\Chat\Contracts\Requests\GetConversationByUserIdRequestContract;
use RonasIT\Chat\Contracts\Requests\GetConversationRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchConversationsRequestContract;
use RonasIT\Chat\Contracts\Resources\ConversationCollectionResourceContract;
use RonasIT\Chat\Contracts\Resources\ConversationResourceContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Http\Resources\ConversationCollectionResource;
use RonasIT\Chat\Http\Resources\ConversationResource;
use Symfony\Component\HttpFoundation\Response;

class ConversationController extends Controller
{
    public function get(GetConversationRequestContract $request, ConversationServiceContract $service, int $id): ConversationResourceContract
    {
        $result = $service
            ->with($request->input('with', []))
            ->withCount($request->input('with_count', []))
            ->find($id);

        return ConversationResource::make($result);
    }

    public function getByUserId(GetConversationByUserIdRequestContract $request, ConversationServiceContract $service, int $userId): ConversationResourceContract|Response
    {
        $result = $service
            ->with($request->input('with', []))
            ->withCount($request->input('with_count', []))
            ->getPrivate($request->user()->id, $userId);

        return (is_null($result))
            ? response()->noContent()
            : ConversationResource::make($result);
    }

    public function search(SearchConversationsRequestContract $request, ConversationServiceContract $service): ConversationCollectionResourceContract
    {
        $result = $service->search($request->onlyValidated());

        return ConversationCollectionResource::make($result);
    }

    public function delete(DeleteConversationRequestContract $request, ConversationServiceContract $service, int $id): Response
    {
        $service->delete($id);

        return response()->noContent();
    }
}
