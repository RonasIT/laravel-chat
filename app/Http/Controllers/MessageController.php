<?php

namespace App\Http\Controllers;

use App\Contracts\Requests\CreateMessageRequestContract;
use App\Contracts\Requests\ReadMessagesRequestContract;
use App\Contracts\Requests\SearchMessagesRequestContract;
use App\Contracts\Services\MessageServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function create(CreateMessageRequestContract $request, MessageServiceContract $service): JsonResponse
    {
        $result = $service->create($request->onlyValidated());

        return response()->json($result);
    }

    public function read(ReadMessagesRequestContract $request, MessageServiceContract $service, $fromMessageId): Response
    {
        $service->markAsReadMessages($fromMessageId);

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function search(SearchMessagesRequestContract $request, MessageServiceContract $service): JsonResponse
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
