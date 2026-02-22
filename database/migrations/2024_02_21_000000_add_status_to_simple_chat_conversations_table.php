<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('simple_chat_conversations', function (Blueprint $table) {
            $table->string('status')->default('open')->after('participants');
        });
    }

    public function down()
    {
        Schema::table('simple_chat_conversations', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
