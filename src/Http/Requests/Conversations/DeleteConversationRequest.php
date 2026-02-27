<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use RonasIT\Chat\Contracts\Requests\DeleteConversationRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Enums\Conversation\TypeEnum;
use RonasIT\Chat\Models\Conversation;

class DeleteConversationRequest extends BaseConversationRequest implements DeleteConversationRequestContract
{
    protected ?Conversation $conversation;

    public function validateResolved(): void
    {
        $this->init();

        parent::validateResolved();

        $this->checkConversationExists();

        $this->checkCanDeleteConversation();
    }

    protected function init(): void
    {
        $this->conversation = app(ConversationServiceContract::class)->find($this->route('id'));
    }

    protected function checkCanDeleteConversation(): void
    {
        match ($this->conversation->type) {
            TypeEnum::Private => $this->checkConversationMembership(),
            TypeEnum::Group => $this->checkConversationCreatorship(),
        };
    }
}
