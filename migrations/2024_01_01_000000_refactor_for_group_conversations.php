<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $usersTableName = app(config('chat.classes.user_model'))->getTable();
        $mediaTableName = app(config('chat.classes.media_model'))->getTable();

        Schema::create('conversation_member', function (Blueprint $table) use ($usersTableName) {
            $table->id();
            $table
                ->foreignId('conversation_id')
                ->references('id')
                ->on('conversations')
                ->cascadeOnDelete();
            $table
                ->foreignId('member_id')
                ->references('id')
                ->on($usersTableName)
                ->cascadeOnDelete();
            $table->unique(['conversation_id', 'member_id']);
        });

        DB::statement('
            INSERT INTO conversation_member (conversation_id, member_id)
            SELECT id, sender_id FROM conversations WHERE sender_id IS NOT NULL
            ON CONFLICT DO NOTHING
        ');

        DB::statement('
            INSERT INTO conversation_member (conversation_id, member_id)
            SELECT id, recipient_id FROM conversations
            WHERE recipient_id IS NOT NULL AND recipient_id != sender_id
            ON CONFLICT DO NOTHING
        ');

        Schema::table('conversations', function (Blueprint $table) use ($usersTableName, $mediaTableName) {
            $table
                ->string('title')
                ->nullable()
                ->after('id');
            $table
                ->foreignId('cover_id')
                ->nullable()
                ->references('id')
                ->on($mediaTableName)
                ->nullOnDelete();
            $table
                ->foreignId('creator_id')
                ->nullable()
                ->references('id')
                ->on($usersTableName)
                ->cascadeOnDelete();
            $table->enum('type', ['private', 'group'])->nullable();
        });

        DB::table('conversations')->whereNull('type')->update(['type' => 'private']);

        DB::statement('ALTER TABLE conversations ALTER COLUMN "type" SET NOT NULL');

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['recipient_id']);
            $table->dropColumn(['sender_id', 'recipient_id']);
        });

        Schema::table('conversation_member', function (Blueprint $table) {
            $table
                ->foreignId('last_read_message_id')
                ->nullable()
                ->references('id')
                ->on('messages')
                ->nullOnDelete();
        });

        DB::statement('
            UPDATE conversation_member
            SET last_read_message_id = (
                SELECT MAX(id)
                FROM messages
                WHERE messages.conversation_id = conversation_member.conversation_id
                  AND messages.recipient_id = conversation_member.member_id
                  AND messages.is_read = TRUE
            )
        ');

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['recipient_id']);
            $table->dropColumn(['recipient_id', 'is_read']);
        });
    }

    public function down(): void
    {
        $usersTableName = app(config('chat.classes.user_model'))->getTable();

        Schema::table('messages', function (Blueprint $table) use ($usersTableName) {
            $table
                ->foreignId('recipient_id')
                ->nullable()
                ->references('id')
                ->on($usersTableName)
                ->cascadeOnDelete();
            $table->boolean('is_read')->default(false);
        });

        Schema::table('conversation_member', function (Blueprint $table) {
            $table->dropForeign(['last_read_message_id']);
            $table->dropColumn('last_read_message_id');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('conversations', function (Blueprint $table) use ($usersTableName) {
            $table->dropForeign(['cover_id']);
            $table->dropForeign(['creator_id']);
            $table->dropColumn(['title', 'cover_id', 'creator_id']);

            $table
                ->foreignId('sender_id')
                ->nullable()
                ->references('id')
                ->on($usersTableName)
                ->cascadeOnDelete();
            $table
                ->foreignId('recipient_id')
                ->nullable()
                ->references('id')
                ->on($usersTableName)
                ->onDelete('cascade');
        });

        Schema::dropIfExists('conversation_member');
    }
};
