<?php

namespace RonasIT\Chat;

use RonasIT\Chat\Enums\ChatRouteActionEnum;
use RonasIT\Chat\Http\Controllers\ConversationController;
use RonasIT\Chat\Http\Controllers\MessageController;
use RonasIT\Chat\Models\Conversation;

class ChatRouter
{
    public static bool $isBlockedBaseRoutes = false;

    public function chat()
    {
        return function (ChatRouteActionEnum ...$options)  {

            ChatRouter::$isBlockedBaseRoutes = true;

            $defaultOptions = [
                'conversations_search' => false,
                'conversations_delete' => false,
                'conversations_get' => false,
                'conversations_get_by_user' => false,
                'messages_search' => false,
                'messages_create' => false,
                'messages_read' => false,
            ];

            $options = array_column($options, 'value');

            $options = array_combine(array_values($options), array_values($options));

            if (empty($options)){
                $options = array_fill_keys(array_keys($defaultOptions), true);
            } else {
                $options = array_merge($defaultOptions, array_fill_keys(array_keys($options), true));
            }

            $this->controller(ConversationController::class)->group(function () use ($options) {
                when($options['conversations_search'], fn () => $this->get('conversations', 'search')->name('conversations.search'));
                when($options['conversations_get'], fn () => $this->get('conversations/{id}', 'get')->name('conversations.get'));
                when($options['conversations_delete'], fn () => $this->delete('conversations/{id}', 'delete')->name('conversations.delete'));
                when($options['conversations_get_by_user_id'], fn () => $this->post('users/{userId}/conversation', 'getByUserId')->name('conversations.get_by_user_id'));
            });

            $this->controller(MessageController::class)->group(function () use ($options) {
                when($options['messages_search'], fn () => $this->get('messages', 'search')->name('messages.search'));
                when($options['messages_create'], fn () => $this->post('messages', 'create')->name('messages.create'));
                when($options['messages_read'], fn () => $this->put('messages/{id}/read', 'read')->name('messages.read'));
            });
        };
    }
}