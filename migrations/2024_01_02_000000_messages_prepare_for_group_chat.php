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

        Schema::create('read_messages', function (Blueprint $table) use ($usersTableName) {
            $table->id();
            $table
                ->foreignId('message_id')
                ->references('id')
                ->on('messages')
                ->cascadeOnDelete();
            $table
                ->foreignId('member_id')
                ->references('id')
                ->on($usersTableName)
                ->cascadeOnDelete();
            $table->unique(['message_id', 'member_id']);
            $table->timestamps();
        });

        DB::statement('
            INSERT INTO read_messages (message_id, member_id, created_at, updated_at)
            SELECT id, recipient_id, created_at, created_at
            FROM messages
            WHERE is_read = true
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

        Schema::dropIfExists('read_messages');
    }
};
