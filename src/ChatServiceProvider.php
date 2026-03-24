<?php

namespace RonasIT\Chat;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use RonasIT\Chat\Contracts\Notifications\ConversationCreatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\ConversationDeletedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\ConversationUpdatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\MessageCreatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\MessageUpdatedNotificationContract;
use RonasIT\Chat\Contracts\Notifications\Resources\Broadcast\ConversationResourceContract as ConversationBroadcastResourceContract;
use RonasIT\Chat\Contracts\Notifications\Resources\Broadcast\MessageResourceContract as MessageBroadcastResourceContract;
use RonasIT\Chat\Contracts\Requests\CreateMessageRequestContract;
use RonasIT\Chat\Contracts\Requests\DeleteConversationRequestContract;
use RonasIT\Chat\Contracts\Requests\GetConversationByUserIdRequestContract;
use RonasIT\Chat\Contracts\Requests\GetConversationRequestContract;
use RonasIT\Chat\Contracts\Requests\PinMessageRequestContract;
use RonasIT\Chat\Contracts\Requests\ReadMessagesRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchConversationsRequestContract;
use RonasIT\Chat\Contracts\Requests\SearchMessagesRequestContract;
use RonasIT\Chat\Contracts\Requests\UnpinMessageRequestContract;
use RonasIT\Chat\Contracts\Resources\ConversationResourceContract;
use RonasIT\Chat\Contracts\Resources\MessageResourceContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Contracts\Services\MessageServiceContract;
use RonasIT\Chat\Http\Requests\Conversations\DeleteConversationRequest;
use RonasIT\Chat\Http\Requests\Conversations\GetConversationByUserIdRequest;
use RonasIT\Chat\Http\Requests\Conversations\GetConversationRequest;
use RonasIT\Chat\Http\Requests\Conversations\SearchConversationsRequest;
use RonasIT\Chat\Http\Requests\Messages\CreateMessageRequest;
use RonasIT\Chat\Http\Requests\Messages\PinMessageRequest;
use RonasIT\Chat\Http\Requests\Messages\ReadMessagesRequest;
use RonasIT\Chat\Http\Requests\Messages\SearchMessagesRequest;
use RonasIT\Chat\Http\Requests\Messages\UnpinMessageRequest;
use RonasIT\Chat\Http\Resources\ConversationResource;
use RonasIT\Chat\Http\Resources\MessageResource;
use RonasIT\Chat\Notifications\ConversationCreatedNotification;
use RonasIT\Chat\Notifications\ConversationDeletedNotification;
use RonasIT\Chat\Notifications\ConversationUpdatedNotification;
use RonasIT\Chat\Notifications\MessageCreatedNotification;
use RonasIT\Chat\Notifications\MessageUpdatedNotification;
use RonasIT\Chat\Notifications\Resources\Broadcast\ConversationResource as ConversationBroadcastResource;
use RonasIT\Chat\Notifications\Resources\Broadcast\MessageResource as MessageBroadcastResource;
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

        $this->publishesMigrations([
            __DIR__ . '/../migrations' => database_path('migrations'),
        ]);

        $this->publishes([
            __DIR__ . '/../config/chat.php' => config_path('chat.php'),
        ]);
    }

    public function register(): void
    {
        $this->app->bind(CreateMessageRequestContract::class, CreateMessageRequest::class);
        $this->app->bind(PinMessageRequestContract::class, PinMessageRequest::class);
        $this->app->bind(UnpinMessageRequestContract::class, UnpinMessageRequest::class);
        $this->app->bind(ReadMessagesRequestContract::class, ReadMessagesRequest::class);
        $this->app->bind(GetConversationRequestContract::class, GetConversationRequest::class);
        $this->app->bind(GetConversationByUserIdRequestContract::class, GetConversationByUserIdRequest::class);
        $this->app->bind(DeleteConversationRequestContract::class, DeleteConversationRequest::class);
        $this->app->bind(SearchConversationsRequestContract::class, SearchConversationsRequest::class);
        $this->app->bind(SearchMessagesRequestContract::class, SearchMessagesRequest::class);

        $this->app->bind(ConversationServiceContract::class, ConversationService::class);
        $this->app->bind(MessageServiceContract::class, MessageService::class);

        $this->app->bind(MessageCreatedNotificationContract::class, MessageCreatedNotification::class);
        $this->app->bind(MessageUpdatedNotificationContract::class, MessageUpdatedNotification::class);
        $this->app->bind(ConversationDeletedNotificationContract::class, ConversationDeletedNotification::class);
        $this->app->bind(ConversationCreatedNotificationContract::class, ConversationCreatedNotification::class);
        $this->app->bind(ConversationUpdatedNotificationContract::class, ConversationUpdatedNotification::class);

        $this->app->bind(ConversationResourceContract::class, ConversationResource::class);
        $this->app->bind(MessageResourceContract::class, MessageResource::class);

        $this->app->alias(MessageResource::class, MessageResourceContract::class);
        $this->app->alias(ConversationResource::class, ConversationResourceContract::class);

        $this->app->bind(ConversationBroadcastResourceContract::class, ConversationBroadcastResource::class);
        $this->app->bind(MessageBroadcastResourceContract::class, MessageBroadcastResource::class);
    }
}
