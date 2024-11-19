<?php

namespace RonasIT\Chat;

use RonasIT\Chat\Http\Controllers\ConversationController;
use RonasIT\Chat\Http\Controllers\MessageController;
use RonasIT\Chat\Models\Conversation;

class ChatRouter
{
    public static bool $isBlockedBaseRoutes = false;

    public function chat()
    {
        return function (array $options = [])  {

            ChatRouter::$isBlockedBaseRoutes = true;

            $options = [
                'conversations_search' => $options['conversations_search'] ?? true,
                'conversations_delete' => $options['conversations_delete'] ?? true,
                'conversations_get' => $options['conversations_get'] ?? true,
                'conversations_get_by_user_id' => $options['conversations_get_by_user_id'] ?? true,
                'messages_search' => $options['messages_search'] ?? true,
                'messages_create' => $options['messages_create'] ?? true,
                'messages_read' => $options['messages_read'] ?? true,
            ];

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