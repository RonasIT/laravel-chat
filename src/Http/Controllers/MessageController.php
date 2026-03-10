<?php

namespace RonasIT\Chat\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use RonasIT\Chat\Contracts\Requests\CreateMessageRequestContract;
use RonasIT\Chat\Contracts\Requests\PinMessageRequestContract;
use RonasIT\Chat\Contracts\Requests\ReadMessagesRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchMessagesRequestContract;
use RonasIT\Chat\Contracts\Resources\MessageResourceContract;
use RonasIT\Chat\Contracts\Resources\MessagesCollectionResourceContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Http\Resources\MessageResource;
use RonasIT\Chat\Http\Resources\MessagesCollectionResource;

class MessageController extends Controller
{
    public function create(CreateMessageRequestContract $request, MessageServiceContract $service): MessageResourceContract
    {
        $result = $service->create($request->onlyValidated());

        return MessageResource::make($result);
    }

    public function search(SearchMessagesRequestContract $request, MessageServiceContract $service): MessagesCollectionResourceContract
    {
        $result = $service->search($request->onlyValidated());

        return MessagesCollectionResource::make($result);
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
