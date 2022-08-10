<?php

namespace RonasIT\Chat\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use RonasIT\Chat\Contracts\Requests\CreateMessageRequestContract;
use RonasIT\Chat\Contracts\Requests\ReadMessageRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchMessagesRequestContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function create(CreateMessageRequestContract $request, MessageServiceContract $service): JsonResponse
    {
        $data = $request->onlyValidated();

        $result = $service->create($data);

        return response()->json($result);
    }

    public function read(ReadMessageRequestContract $request, MessageServiceContract $service, $id)
    {
        $service->markAsReadMessages($id);

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function search(SearchMessagesRequestContract $request, MessageServiceContract $service): JsonResponse
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
