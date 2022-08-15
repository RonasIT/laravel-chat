<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MessagesAddAttachmentIdField extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->integer('attachment_id')->nullable();
            $table->foreign('attachment_id')->references('id')->on('media')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('attachment_id');
        });
    }
}
