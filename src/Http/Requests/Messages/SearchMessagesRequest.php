<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use RonasIT\Chat\Contracts\Requests\SearchMessagesRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Models\Conversation;
use RonasIT\Support\Http\BaseRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SearchMessagesRequest extends BaseRequest implements SearchMessagesRequestContract
{
    protected ?Conversation $conversation;

    public function authorize(): bool
    {
        return $this->conversation->hasMember($this->user());
    }

    public function validateResolved(): void
    {
        $this->init();

        $this->checkConversationExists();

        parent::validateResolved();
    }

    public function rules(): array
    {
        return [
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'nullable|string',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with' => 'array',
            'with.*' => 'string|required|in:' . $this->getAvailableRelations(),
        ];
    }

    protected function init(): void
    {
        $this->conversation = app(ConversationServiceContract::class)->find($this->route('conversationId'));
    }

    protected function getAvailableRelations(): string
    {
        return implode(',', [
            'conversation',
            'sender',
            'attachment',
        ]);
    }

    protected function checkConversationExists(): void
    {
        if (empty($this->conversation)) {
            throw new NotFoundHttpException(__('chat::validation.exceptions.not_found', ['entity' => 'Conversation']));
        }
    }
}
