<?php

namespace RonasIT\Chat\Tests;

use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;
use RonasIT\Chat\Enums\Conversation\TypeEnum;
use RonasIT\Chat\Models\Conversation;

class ConversationModelTest extends TestCase
{
    public function testIsPrivateReturnsTrueForPrivateConversation()
    {
        $conversation = new Conversation(['type' => TypeEnum::Private]);

        $this->assertTrue($conversation->isPrivate());
        $this->assertFalse($conversation->isGroup());
    }

    public function testIsGroupReturnsTrueForGroupConversation()
    {
        $conversation = new Conversation(['type' => TypeEnum::Group]);

        $this->assertTrue($conversation->isGroup());
        $this->assertFalse($conversation->isPrivate());
    }

    public static function getBuildTitleExpressionCases(): array
    {
        return [
            'single_column' => [
                'columns' => ['name'],
                'separators' => [],
                'expected' => 'name',
            ],
            'first_and_last_name' => [
                'columns' => ['first_name', 'last_name'],
                'separators' => [' '],
                'expected' => "TRIM(BOTH ' ' FROM COALESCE(first_name, '') || COALESCE(' ' || last_name, ''))",
            ],
            'first_last_name_and_job' => [
                'columns' => ['first_name', 'last_name', 'job'],
                'separators' => [' ', ' - '],
                'expected' => "TRIM(BOTH '  - ' FROM COALESCE(first_name, '') || COALESCE(' ' || last_name, '') || COALESCE(' - ' || job, ''))",
            ],
        ];
    }

    #[DataProvider('getBuildTitleExpressionCases')]
    public function testBuildTitleExpression(array $columns, array $separators, string $expected): void
    {
        Config::set('chat.classes.user.columns.full_name_separator', $separators);

        $method = new ReflectionMethod(Conversation::class, 'buildTitleExpression');
        $result = $method->invoke(new Conversation(), $columns);

        $this->assertSame($expected, $result);
    }
}
