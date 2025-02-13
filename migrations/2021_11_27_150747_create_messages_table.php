<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up(): void
    {
        $usersTableName = app(config('chat.classes.user_model'))->getTable();

        Schema::create('messages', function (Blueprint $table) use ($usersTableName) {
            $table->increments('id');

            $table->foreignId('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->foreignId('sender_id')->references('id')->on($usersTableName)->onDelete('cascade');
            $table->foreignId('recipient_id')->references('id')->on($usersTableName)->onDelete('cascade');

            $table->text('text');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
}
