<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Requests\CreateMessageRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Support\Http\BaseRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateMessageRequest extends BaseRequest implements CreateMessageRequestContract
{
    protected ?Conversation $conversation;

    public function authorize(): bool
    {
        return $this->conversation->isMember($this->user());
    }

    public function rules(): array
    {
        $mediaTableName = app(config('chat.classes.media_model'))->getTable();

        return [
            'text' => 'string|required',
            'attachment_id' => "integer|exists:{$mediaTableName},id",
        ];
    }

    public function validateResolved(): void
    {
        $this->init();

        $this->checkConversationExists();

        parent::validateResolved();
    }

    protected function init(): void
    {
        $this->conversation = app(ConversationServiceContract::class)->find($this->route('conversation_id'));
    }

    protected function checkConversationExists(): void
    {
        if (empty($this->conversation)) {
            throw new NotFoundHttpException(__('chat::validation.exceptions.not_found', ['entity' => 'Conversation']));
        }
    }
}
