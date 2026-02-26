<?php

namespace RonasIT\Chat\Enums;

enum ChatRouteActionEnum: string
{
    case ConversationSearch = 'conversations_search';
    case ConversationGet = 'conversations_get';
    case ConversationUpdate = 'conversations_update';
    case ConversationDelete = 'conversations_delete';
    case ConversationGetOrCreatePrivate = 'conversations_get_or_create_private';

    case MessageSearch = 'messages_search';
    case MessageCreate = 'messages_create';
    case MessageRead = 'messages_read';
}
