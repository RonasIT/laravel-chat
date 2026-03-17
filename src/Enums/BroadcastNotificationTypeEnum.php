<?php

namespace RonasIT\Chat\Enums;

enum BroadcastNotificationTypeEnum: string
{
    case ConversationCreated = 'conversation.created';
    case ConversationUpdated = 'conversation.updated';
    case ConversationDeleted = 'conversation.deleted';
    case MessageCreated = 'message.created';
}
