<?php

namespace App\Providers;

use App\Contracts\Notifications\NewMessageNotificationContract;
use App\Contracts\Requests\CreateMessageRequestContract;
use App\Contracts\Requests\DeleteConversationRequestContract;
use App\Contracts\Requests\GetConversationByUserIdRequestContract;
use App\Contracts\Requests\GetConversationRequestContract;
use App\Contracts\Requests\ReadMessagesRequestContract;
use App\Contracts\Requests\SearchConversationsRequestContract;
use App\Contracts\Requests\SearchMessagesRequestContract;
use App\Contracts\Services\ConversationServiceContract;
use App\Contracts\Services\MessageServiceContract;
use App\Http\Requests\Conversations\DeleteConversationRequest;
use App\Http\Requests\Conversations\GetConversationByUserIdRequest;
use App\Http\Requests\Conversations\GetConversationRequest;
use App\Http\Requests\Conversations\SearchConversationsRequest;
use App\Http\Requests\Messages\CreateMessageRequest;
use App\Http\Requests\Messages\ReadMessagesRequest;
use App\Http\Requests\Messages\SearchMessagesRequest;
use App\Notifications\NewMessageNotification;
use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    public function register()
    {
        $this->app->bind(CreateMessageRequestContract::class, CreateMessageRequest::class);
        $this->app->bind(GetConversationRequestContract::class, GetConversationRequest::class);
        $this->app->bind(GetConversationByUserIdRequestContract::class, GetConversationByUserIdRequest::class);
        $this->app->bind(DeleteConversationRequestContract::class, DeleteConversationRequest::class);
        $this->app->bind(SearchConversationsRequestContract::class, SearchConversationsRequest::class);
        $this->app->bind(SearchMessagesRequestContract::class, SearchMessagesRequest::class);
        $this->app->bind(ReadMessagesRequestContract::class, ReadMessagesRequest::class);

        $this->app->bind(ConversationServiceContract::class, ConversationService::class);
        $this->app->bind(MessageServiceContract::class, MessageService::class);

        $this->app->bind(NewMessageNotificationContract::class, NewMessageNotification::class);
    }
}
