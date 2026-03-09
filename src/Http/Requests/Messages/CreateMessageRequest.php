<?php

namespace RonasIT\Chat\Http\Requests\Messages;

use Illuminate\Validation\Validator;
use RonasIT\Chat\Contracts\Requests\CreateMessageRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Support\Http\BaseRequest;

class CreateMessageRequest extends BaseRequest implements CreateMessageRequestContract
{
    public function rules(): array
    {
        $mediaTableName = app(config('chat.classes.media_model'))->getTable();
        $userTableName = app(config('chat.classes.user_model'))->getTable();

        return [
            'recipient_id' => "required_without:conversation_id|prohibits:conversation_id|integer|exists:{$userTableName},id",
            'conversation_id' => 'required_without:recipient_id|prohibits:recipient_id|integer',
            'text' => 'string|required',
            'attachment_id' => "integer|exists:{$mediaTableName},id",
        ];
    }

    public function after(): array
    {
        return [
            fn (Validator $validator) => !$validator->errors()->isEmpty() || $this->checkSelfMessage($validator),
            fn (Validator $validator) => !$validator->errors()->isEmpty() || $this->checkConversation($validator),
        ];
    }

    protected function checkSelfMessage(Validator $validator): void
    {
        if ($this->has('recipient_id') && $this->user()->id === $this->input('recipient_id')) {
            $validator->errors()->add('recipient_id', __('chat::validation.custom.recipient_same_as_sender'));
        }
    }

    protected function checkConversation(Validator $validator): void
    {
        if (!$this->has('conversation_id')) {
            return;
        }

        $conversation = app(ConversationServiceContract::class)->find($this->input('conversation_id'));

        if (!$conversation?->hasMember($this->user())) {
            $validator->errors()->add('conversation_id', __('validation.exists', ['attribute' => 'conversation id']));
        }
    }
}
