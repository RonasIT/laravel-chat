<?php

namespace RonasIT\Chat\Http\Requests\Conversations;

use Illuminate\Validation\Validator;
use RonasIT\Chat\Contracts\Requests\UpdateConversationRequestContract;
use RonasIT\Chat\Contracts\Services\ConversationMemberServiceContract;
use RonasIT\Chat\Contracts\Services\ConversationServiceContract;
use RonasIT\Chat\Models\Conversation;

class UpdateConversationRequest extends BaseConversationRequest implements UpdateConversationRequestContract
{
    protected ?Conversation $conversation;

    public function authorize(): bool
    {
        return $this->conversation->isCreator($this->user());
    }

    public function rules(): array
    {
        $mediaTableName = app(config('chat.classes.media_model'))->getTable();

        return [
            'title' => 'filled|string|max:255',
            'cover_id' => "nullable|integer|exists:{$mediaTableName},id",
            'member_ids' => 'filled|array',
            'member_ids.*' => 'integer|distinct',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isEmpty()) {
                    $this->checkMembersExists($validator);
                }
            },
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
        $this->conversation = app(ConversationServiceContract::class)->find($this->route('id'));
    }

    protected function checkMembersExists(Validator $validator): void
    {
        $memberIds = $this->input('member_ids');

        if ($this->has('member_ids') && !empty($memberIds)) {
            $members = app(ConversationMemberServiceContract::class)->getByList($memberIds);

            $missingIds = array_diff($memberIds, $members->pluck('id')->toArray());

            if (!empty($missingIds)) {
                $validator->errors()->add('member_ids', __('validation.exists', ['attribute' => 'member ids']));
            }
        }
    }
}
