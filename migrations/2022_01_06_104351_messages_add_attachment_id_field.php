<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MessagesAddAttachmentIdField extends Migration
{
    public function up(): void
    {
        $mediaTableName = app(config('chat.classes.media_model'))->getTable();

        Schema::table('messages', function (Blueprint $table) use ($mediaTableName) {
            $table->integer('attachment_id')->nullable();
            $table->foreign('attachment_id')->references('id')->on($mediaTableName)->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('attachment_id');
        });
    }
}
