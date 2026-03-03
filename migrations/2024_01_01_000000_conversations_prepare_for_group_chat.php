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

        Schema::table('conversations', function (Blueprint $table) use ($usersTableName, $mediaTableName) {
            $table
                ->foreignId('creator_id')
                ->nullable()
                ->references('id')
                ->on($usersTableName)
                ->cascadeOnDelete();
            $table->enum('type', ['private', 'group'])->nullable();
            $table->string('title')->nullable();
            $table
                ->foreignId('cover_id')
                ->nullable()
                ->references('id')
                ->on($mediaTableName)
                ->nullOnDelete();
        });

        DB::statement("UPDATE conversations SET type = 'private'");
        DB::statement('ALTER TABLE conversations ALTER COLUMN type SET NOT NULL');

        DB::statement('
            INSERT INTO conversation_member (conversation_id, member_id)
            SELECT id, sender_id FROM conversations
        ');

        DB::statement('
            INSERT INTO conversation_member (conversation_id, member_id)
            SELECT id, recipient_id FROM conversations
        ');

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['recipient_id']);
            $table->dropColumn(['sender_id', 'recipient_id']);
        });
    }

    public function down(): void
    {
        $usersTableName = app(config('chat.classes.user_model'))->getTable();

        Schema::table('conversations', function (Blueprint $table) use ($usersTableName) {
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
                ->cascadeOnDelete();
            $table->dropForeign(['creator_id']);
            $table->dropForeign(['cover_id']);
            $table->dropColumn([
                'creator_id',
                'type',
                'title',
                'cover_id',
            ]);
        });

        Schema::dropIfExists('conversation_member');
    }
};
