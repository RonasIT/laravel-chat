<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use RonasIT\Support\Traits\MigrationTrait;

class CreateConversationsTable extends Migration
{
    use MigrationTrait;

    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->increments('id');

            $table->foreignId('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('recipient_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversations');
    }
}
