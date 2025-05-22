<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_unread_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')
                ->constrained('chats');
            $table->foreignId('chat_message_id')
                ->constrained('chat_messages');
            $table->foreignId('user_id')
                ->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_unread_messages');
    }
};
