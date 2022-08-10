<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReactionsTable extends Migration
{
    public function up()
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unique(['sender_id', 'recipient_id']);
            $table->enum('type', ['like', 'dislike'])->nullable();
            $table->timestamps();

            $table->foreignId('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('recipient_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reactions');
    }
}
