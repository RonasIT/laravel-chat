<?php

namespace RonasIT\Chat\Enums;

enum ChatRouteActionEnum: string
{
    case ConversationSearch = 'conversations_search';
    case ConversationDelete = 'conversations_delete';
    case ConversationGet = 'conversations_get';
    case ConversationGetByUser = 'conversations_get_by_user';

    case MessageSearch = 'messages_search';
    case MessageCreate = 'messages_create';
    case MessageRead = 'messages_read';
}