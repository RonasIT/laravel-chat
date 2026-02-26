<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use Illuminate\Validation\Validator;
use RonasIT\Chat\Contracts\Requests\GetOrCreatePrivateConversationRequestContract;
use RonasIT\Support\Http\BaseRequest;

class GetOrCreatePrivateConversationRequest extends BaseRequest implements GetOrCreatePrivateConversationRequestContract
{
    public function rules(): array
    {
        $usersTableName = app(config('chat.classes.user_model'))->getTable();

        return [
            'participant_id' => "required|integer|exists:{$usersTableName},id",
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isEmpty()) {
                    $this->checkParticipant($validator);
                }
            },
        ];
    }

    protected function checkParticipant(Validator $validator): void
    {
        if ($this->user()->id === $this->input('participant_id')) {
            $validator->errors()->add('participant_id', __('chat::validation.exceptions.self_conversation'));
        }
    }
}
