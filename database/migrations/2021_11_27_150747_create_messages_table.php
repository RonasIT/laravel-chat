<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');

            $table->foreignId('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $usersTableName = config('chat.database.tables.users');
            $table->foreignId('sender_id')->references('id')->on($usersTableName)->onDelete('cascade');
            $table->foreignId('recipient_id')->references('id')->on($usersTableName)->onDelete('cascade');

            $table->text('text');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
