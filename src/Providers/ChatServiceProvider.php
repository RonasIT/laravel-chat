<?php

namespace RonasIT\Chat\Providers;

use Illuminate\Support\ServiceProvider;
use RonasIT\Chat\Contracts\Notifications\NewMessageNotificationContract;
use RonasIT\Chat\Contracts\Requests\CreateMessageRequestContract;
use RonasIT\Chat\Contracts\Requests\GetConversationRequestContract;
use RonasIT\Chat\Contracts\Requests\ReadMessagesRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchConversationsRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchMessagesRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Http\Requests\Conversations\GetConversationRequest;
use RonasIT\Chat\Http\Requests\Conversations\SearchConversationsRequest;
use RonasIT\Chat\Http\Requests\Messages\CreateMessageRequest;
use RonasIT\Chat\Http\Requests\Messages\ReadMessagesRequest;
use RonasIT\Chat\Http\Requests\Messages\SearchMessagesRequest;
use RonasIT\Chat\Notifications\NewMessageNotification;
use RonasIT\Chat\Services\ConversationService;
use RonasIT\Chat\Services\MessageService;

class ChatServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    public function register()
    {
        $this->app->bind(CreateMessageRequestContract::class, CreateMessageRequest::class);
        $this->app->bind(GetConversationRequestContract::class, GetConversationRequest::class);
        $this->app->bind(SearchConversationsRequestContract::class, SearchConversationsRequest::class);
        $this->app->bind(SearchMessagesRequestContract::class, SearchMessagesRequest::class);
        $this->app->bind(ReadMessagesRequestContract::class, ReadMessagesRequest::class);

        $this->app->bind(ConversationServiceContract::class, ConversationService::class);
        $this->app->bind(MessageServiceContract::class, MessageService::class);

        $this->app->bind(NewMessageNotificationContract::class, NewMessageNotification::class);
    }
}
