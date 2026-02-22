<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('simple_chat_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('storage_path')->nullable(); // For SQLite sharding
            $table->json('participants')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('simple_chat_conversations');
    }
};
