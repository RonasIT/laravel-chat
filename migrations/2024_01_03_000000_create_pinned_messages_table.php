<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinned_messages', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('conversation_id')
                ->references('id')
                ->on('conversations')
                ->cascadeOnDelete();
            $table
                ->foreignId('message_id')
                ->references('id')
                ->on('messages')
                ->cascadeOnDelete();
            $table->unique(['conversation_id', 'message_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinned_messages');
    }
};
