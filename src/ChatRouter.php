<?php

namespace RonasIT\Chat;

use Closure;
use Illuminate\Support\Arr;
use RonasIT\Chat\Enums\ChatRouteActionEnum;
use RonasIT\Chat\Http\Controllers\ConversationController;
use RonasIT\Chat\Http\Controllers\MessageController;

class ChatRouter
{
    public static bool $isBlockedBaseRoutes = false;

    public function chat(): Closure
    {
        return function (ChatRouteActionEnum ...$options) {
            ChatRouter::$isBlockedBaseRoutes = true;

            $defaultOptions = [
                'conversations_search' => true,
                'conversations_get' => true,
                'conversations_update' => true,
                'conversations_delete' => true,
                'conversations_get_or_create_private' => true,
                'messages_search' => true,
                'messages_create' => true,
                'messages_read' => true,
            ];

            if (!empty($options)) {
                $options = collect($options);

                $defaultOptions = Arr::map($defaultOptions, fn ($value, $defaultOption) => $options->contains('value', $defaultOption));
            }

            $this->controller(ConversationController::class)->group(function () use ($defaultOptions) {
                when($defaultOptions['conversations_search'], fn () => $this->get('conversations', 'search')->name('conversations.search'));
                when($defaultOptions['conversations_get'], fn () => $this->get('conversations/{id}', 'get')->name('conversations.get'));
                when($defaultOptions['conversations_update'], fn () => $this->put('conversations/{id}', 'update')->name('conversations.update'));
                when($defaultOptions['conversations_delete'], fn () => $this->delete('conversations/{id}', 'delete')->name('conversations.delete'));
                when($defaultOptions['conversations_get_or_create_private'], fn () => $this->post('conversations/private', 'getOrCreatePrivate')->name('conversations.get_or_create_private'));
            });

            $this->controller(MessageController::class)->group(function () use ($defaultOptions) {
                when($defaultOptions['messages_search'], fn () => $this->get('messages', 'search')->name('messages.search'));
                when($defaultOptions['messages_create'], fn () => $this->post('messages/{conversation_id}', 'create')->name('messages.create'));
                when($defaultOptions['messages_read'], fn () => $this->put('messages/{id}/read', 'read')->name('messages.read'));
            });
        };
    }
}
