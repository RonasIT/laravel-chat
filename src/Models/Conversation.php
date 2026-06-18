<?php

namespace RonasIT\Chat\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use RonasIT\Chat\Contracts\Models\ConversationModelContract;
use RonasIT\Chat\Contracts\Models\MessageModelContract;
use RonasIT\Chat\Enums\Conversation\TypeEnum;
use RonasIT\Support\Traits\ModelTrait;

class Conversation extends Model implements ConversationModelContract
{
    use ModelTrait;

    protected $fillable = [
        'creator_id',
        'type',
        'title',
        'cover_id',
        'last_updated_at',
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'type' => TypeEnum::class,
    ];

    public function last_message(): HasOne
    {
        return $this->hasOne(app()->getAlias(MessageModelContract::class))->latest();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(app()->getAlias(MessageModelContract::class));
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('chat.classes.user.model'));
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            related: config('chat.classes.user.model'),
            table: 'conversation_member',
            foreignPivotKey: 'conversation_id',
            relatedPivotKey: 'member_id',
        );
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(config('chat.classes.media.model'), 'cover_id');
    }

    protected function title(): Attribute
    {
        return Attribute::make(
            get: function ($value): ?string {
                return ($this->isPrivate())
                    ? Arr::get($this->attributes, 'calculated_title')
                    : $value;
            },
        );
    }

    protected function coverId(): Attribute
    {
        return Attribute::make(
            get: function ($value): ?int {
                return ($this->isPrivate())
                    ? Arr::get($this->attributes, 'calculated_cover_id')
                    : $value;
            },
        );
    }

    public function scopeWithCalculatedIdentity(Builder $query, int $forMemberId): Builder
    {
        $constraint = fn (Builder $query) => $query->where('member_id', '!=', $forMemberId);

        if ($titleColumns = config('chat.classes.user.columns.full_name')) {
            $titleColumn = $this->buildTitleExpression($titleColumns);

            $query->withAggregate(['members as calculated_title' => $constraint], $titleColumn);
        } else {
            trigger_error('chat.classes.user.columns.full_name is not configured, calculated_title will be null.', E_USER_WARNING);
        }

        if ($avatarColumn = config('chat.classes.user.columns.avatar')) {
            $query->withAggregate(['members as calculated_cover_id' => $constraint], $avatarColumn);
        }

        return $query;
    }

    public function scopeWithUnreadMessagesCount(Builder $query, int $memberId): Builder
    {
        return $query->withCount([
            'messages as unread_messages_count' => fn ($query) => $query
                ->whereNot('sender_id', $memberId)
                ->whereDoesntHave('reads', fn ($query) => $query->where('read_messages.member_id', $memberId)),
        ]);
    }

    public function pinned_messages(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                related: app()->getAlias(MessageModelContract::class),
                table: 'pinned_messages',
                foreignPivotKey: 'conversation_id',
                relatedPivotKey: 'message_id',
            )
            ->orderByPivot('id', 'desc')
            ->withTimestamps();
    }

    public function hasMember(Model $member): bool
    {
        return $this->members()->where('member_id', $member->id)->exists();
    }

    public function isCreator(Model $member): bool
    {
        return $this->getAttribute('creator_id') === $member->id;
    }

    public function isGroup(): bool
    {
        return $this->getAttribute('type') === TypeEnum::Group;
    }

    public function isPrivate(): bool
    {
        return $this->getAttribute('type') === TypeEnum::Private;
    }

    public function hasPinnedMessage(int $messageId): bool
    {
        return $this
            ->pinned_messages()
            ->whereKey($messageId)
            ->exists();
    }

    private function buildTitleExpression(array $columns): string
    {
        $expression = array_shift($columns);

        if (!empty($columns)) {
            $separators = config('chat.classes.user.columns.full_name_separator');

            $expression = "COALESCE({$expression}, '')";

            foreach ($columns as $i => $column) {
                $sep = str_replace("'", "''", ($separators[$i] ?? end($separators)) ?: '');
                $expression .= " || COALESCE('{$sep}' || {$column}, '')";
            }
        }

        return $expression;
    }
}
