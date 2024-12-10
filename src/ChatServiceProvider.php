<?php

namespace RonasIT\Chat;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use RonasIT\Chat\Contracts\Notifications\ConversationDeletedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\NewMessageNotificationContract;
use RonasIT\Chat\Contracts\Requests\CreateMessageRequestContract;
use RonasIT\Chat\Contracts\Requests\DeleteConversationRequestContract;
use RonasIT\Chat\Contracts\Requests\GetConversationByUserIdRequestContract;
use RonasIT\Chat\Contracts\Requests\GetConversationRequestContract;
use RonasIT\Chat\Contracts\Requests\ReadMessagesRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchConversationsRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchMessagesRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Http\Requests\Conversations\DeleteConversationRequest;
use RonasIT\Chat\Http\Requests\Conversations\GetConversationByUserIdRequest;
use RonasIT\Chat\Http\Requests\Conversations\GetConversationRequest;
use RonasIT\Chat\Http\Requests\Conversations\SearchConversationsRequest;
use RonasIT\Chat\Http\Requests\Messages\CreateMessageRequest;
use RonasIT\Chat\Http\Requests\Messages\ReadMessagesRequest;
use RonasIT\Chat\Http\Requests\Messages\SearchMessagesRequest;
use RonasIT\Chat\Notifications\ConversationDeletedNotification;
use RonasIT\Chat\Notifications\NewMessageNotification;
use RonasIT\Chat\Services\ConversationService;
use RonasIT\Chat\Services\MessageService;

class ChatServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::mixin(new ChatRouter());

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'chat');
        $this->loadRoutesFrom(__DIR__ . '/Http/routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/Http/routes/channels.php');
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');

        $this->publishes([
            __DIR__. '/../config/chat.php' => config_path('chat.php'),
        ]);
    }

    public function register(): void
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
        $this->app->bind(ConversationDeletedNotificationContract::class, ConversationDeletedNotification::class);
    }
}
