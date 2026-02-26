<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use RonasIT\Chat\Contracts\Requests\DeleteConversationRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Enums\Conversation\TypeEnum;
use RonasIT\Chat\Models\Conversation;

class DeleteConversationRequest extends BaseConversationRequest implements DeleteConversationRequestContract
{
    protected ?Conversation $conversation;

    public function authorize(): bool
    {
        return match ($this->conversation->type) {
            TypeEnum::Group => $this->conversation->isCreator($this->user()),
            TypeEnum::Private => $this->conversation->isMember($this->user()),
        };
    }

    public function validateResolved(): void
    {
        $this->init();

        $this->checkConversationExists();

        parent::validateResolved();
    }

    protected function init(): void
    {
        $this->conversation = app(ConversationServiceContract::class)->find($this->route('id'));
    }
}
