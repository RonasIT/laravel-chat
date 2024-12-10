<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationsTable extends Migration
{
    public function up(): void
    {
        $usersTableName = app(config('chat.classes.user_model'))->getTable();

        Schema::create('conversations', function (Blueprint $table) use ($usersTableName) {
            $table->increments('id');

            $table->foreignId('sender_id')->references('id')->on($usersTableName)->onDelete('cascade');
            $table->foreignId('recipient_id')->references('id')->on($usersTableName)->onDelete('cascade');
            $table->timestamp('last_updated_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
}
