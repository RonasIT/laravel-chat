<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('link');
            $table->string('name')->unique();
            $table->boolean('is_public')->default(false);
            $table->integer('owner_id')->nullable();
            $table->string('preview_id')->nullable();

            if (config('database.default') == 'mysql') {
                $table->jsonb('meta')->nullable();
            } else {
                $table->jsonb('meta')->default('{}');
            }

            $table
                ->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('media');
    }
}
