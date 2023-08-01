<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConversationsTable extends Migration
{
    public function up()
    {
        $usersTableName = app(config('chat.classes.user_model'))->getTable();

        Schema::create('conversations', function (Blueprint $table) use ($usersTableName) {
            $table->increments('id');

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
