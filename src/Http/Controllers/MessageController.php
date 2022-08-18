<?php

namespace RonasIT\Chat\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use RonasIT\Chat\Contracts\Requests\CreateMessageRequestContract;
use RonasIT\Chat\Contracts\Requests\ReadMessageRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchMessagesRequestContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Http\Requests\Messages\CreateMessageRequest;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function create(CreateMessageRequest $request, MessageServiceContract $service): JsonResponse
    {
        $result = $service->create($request->validated());

        return response()->json($result);
    }

    public function read(ReadMessageRequestContract $request, MessageServiceContract $service, $id): Response
    {
        $service->markAsReadMessages($id);

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function search(SearchMessagesRequestContract $request, MessageServiceContract $service): JsonResponse
    {
        $result = $service->search($request->validated());

        return response()->json($result);
    }
}
