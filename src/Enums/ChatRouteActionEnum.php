<?php

namespace RonasIT\Chat\Enums;

enum ChatRouteActionEnum: string
{
    case ConversationsSearch = 'conversations_search';
    case ConversationDelete = 'conversation_delete';
    case ConversationGet = 'conversation_get';
    case ConversationGetByUser = 'conversation_get_by_user';

    case MessagesSearch = 'messages_search';
    case MessageCreate = 'message_create';
    case MessagesRead = 'messages_read';
    case MessagePin = 'message_pin';
    case MessageUnpin = 'message_unpin';
}
