<?php

namespace RonasIT\Chat\Providers;

use Illuminate\Support\ServiceProvider;
use RonasIT\Chat\Contracts\Models\UserContract;
use RonasIT\Chat\Contracts\Requests\CreateMessageRequestContract;
use RonasIT\Chat\Contracts\Requests\GetConversationRequestContract;
use RonasIT\Chat\Contracts\Requests\ReadMessageRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchConversationsRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchMessagesRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Http\Requests\Conversations\GetConversationRequest;
use RonasIT\Chat\Http\Requests\Conversations\SearchConversationsRequest;
use RonasIT\Chat\Http\Requests\Messages\CreateMessageRequest;
use RonasIT\Chat\Http\Requests\Messages\ReadMessageRequest;
use RonasIT\Chat\Http\Requests\Messages\SearchMessagesRequest;
use RonasIT\Chat\Services\ConversationService;
use RonasIT\Chat\Services\MessageService;

class ChatServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom('Ronasit/Chat/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'chat');
        $this->mergeConfigFrom(__DIR__ . '/../config/exponent-push-notifications.php', 'exponent-push-notifications');
        $this->publishes([__DIR__ . '/../../config/example.php' => config_path('exponent-push-notifications.php')]);
    }

    public function register()
    {
        $this->app->bind(CreateMessageRequestContract::class, CreateMessageRequest::class);
        $this->app->bind(GetConversationRequestContract::class, GetConversationRequest::class);
        $this->app->bind(SearchConversationsRequestContract::class, SearchConversationsRequest::class);
        $this->app->bind(SearchMessagesRequestContract::class, SearchMessagesRequest::class);
        $this->app->bind(ReadMessageRequestContract::class, ReadMessageRequest::class);

        $this->app->bind(ConversationServiceContract::class, ConversationService::class);
        $this->app->bind(MessageServiceContract::class, MessageService::class);
    }
}
