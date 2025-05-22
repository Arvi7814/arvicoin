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
        Schema::dropIfExists('user_credentials');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('user_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->string('type');
            $table->foreignId('user_id')
                ->constrained('users');
            $table->timestamps();
        });
    }
};
