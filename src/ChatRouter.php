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
                'conversation_delete' => true,
                'conversation_get' => true,
                'conversation_get_by_user' => true,
                'messages_search' => true,
                'message_create' => true,
                'messages_read' => true,
                'message_pin' => true,
            ];

            if (!empty($options)) {
                $options = collect($options);

                $defaultOptions = Arr::map($defaultOptions, fn ($value, $defaultOption) => $options->contains('value', $defaultOption));
            }

            $this->controller(ConversationController::class)->group(function () use ($defaultOptions) {
                when($defaultOptions['conversations_search'], fn () => $this->get('conversations', 'search')->name('conversations.search'));
                when($defaultOptions['conversation_get'], fn () => $this->get('conversations/{id}', 'get')->name('conversations.get'));
                when($defaultOptions['conversation_delete'], fn () => $this->delete('conversations/{id}', 'delete')->name('conversations.delete'));
                when($defaultOptions['conversation_get_by_user'], fn () => $this->get('users/{userId}/conversation', 'getByUserId')->name('conversations.get_by_user_id'));
            });

            $this->controller(MessageController::class)->group(function () use ($defaultOptions) {
                when($defaultOptions['messages_search'], fn () => $this->get('messages', 'search')->name('messages.search'));
                when($defaultOptions['message_create'], fn () => $this->post('messages', 'create')->name('messages.create'));
                when($defaultOptions['messages_read'], fn () => $this->post('messages/{id}/read-to', 'readUpTo')->name('messages.read'));
                when($defaultOptions['message_pin'], fn () => $this->post('messages/{id}/pin', 'pin')->name('messages.pin'));
            });
        };
    }
}
