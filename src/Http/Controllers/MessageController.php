<?php

namespace RonasIT\Chat\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use RonasIT\Chat\Contracts\Requests\CreateMessageRequestContract;
use RonasIT\Chat\Contracts\Requests\PinMessageRequestContract;
use RonasIT\Chat\Contracts\Requests\ReadMessagesRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchMessagesRequestContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;

class MessageController extends Controller
{
    public function create(CreateMessageRequestContract $request, MessageServiceContract $service): JsonResponse
    {
        $result = $service->create($request->onlyValidated());

        return response()->json($result);
    }

    public function search(SearchMessagesRequestContract $request, MessageServiceContract $service): JsonResponse
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }

    public function readUpTo(ReadMessagesRequestContract $request, MessageServiceContract $service, int $toID): Response
    {
        $service->read($toID);

        return response()->noContent();
    }

    public function pin(PinMessageRequestContract $request, MessageServiceContract $service, int $id): Response
    {
        $service->pin($id);

        return response()->noContent();
    }
}
