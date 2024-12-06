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

            foreach ($options as $option) {
                $defaultOptions[$option->value] = true;
            }

            $this->controller(ConversationController::class)->group(function () use ($defaultOptions) {
                when($defaultOptions['conversations_search'], fn () => $this->get('conversations', 'search')->name('conversations.search'));
                when($defaultOptions['conversations_get'], fn () => $this->get('conversations/{id}', 'get')->name('conversations.get'));
                when($defaultOptions['conversations_delete'], fn () => $this->delete('conversations/{id}', 'delete')->name('conversations.delete'));
                when($defaultOptions['conversations_get_by_user'], fn () => $this->post('users/{userId}/conversation', 'getByUserId')->name('conversations.get_by_user_id'));
            });

            $this->controller(MessageController::class)->group(function () use ($defaultOptions) {
                when($defaultOptions['messages_search'], fn () => $this->get('messages', 'search')->name('messages.search'));
                when($defaultOptions['messages_create'], fn () => $this->post('messages', 'create')->name('messages.create'));
                when($defaultOptions['messages_read'], fn () => $this->put('messages/{id}/read', 'read')->name('messages.read'));
            });
        };
    }
}