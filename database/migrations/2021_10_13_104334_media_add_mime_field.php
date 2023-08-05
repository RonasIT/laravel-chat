<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MediaAddMimeField extends Migration
{
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('link')->nullable()->change();
            $table->string('mime')->nullable();
        });
    }

    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('link')->nullable(false)->change();
            $table->dropColumn(['mime']);
        });
    }
}