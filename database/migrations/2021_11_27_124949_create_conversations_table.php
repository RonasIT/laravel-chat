<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConversationsTable extends Migration
{
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->increments('id');

            $usersTableName = config('chat.database.tables.users');
            $table->foreignId('sender_id')->references('id')->on($usersTableName)->onDelete('cascade');
            $table->foreignId('recipient_id')->references('id')->on($usersTableName)->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversations');
    }
}
