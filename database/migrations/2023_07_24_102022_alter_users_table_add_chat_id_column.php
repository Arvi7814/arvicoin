<?php

use App\Enum\UserState;
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
        Schema::table('users', function (Blueprint $table) {
            $table->string('chat_id')->nullable();
            $table->string('username')->nullable();
            $table->string('state')->default(UserState::INITIAL);
            $table->string('last_message')->nullable();

            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('phone_number')->nullable()->change();
            $table->string('password')->nullable()->change();

            $table->dropIndex('users_phone_number_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('chat_id');
            $table->dropColumn('username');
            $table->dropColumn('state');
            $table->dropColumn('last_message');

            $table->index(['phone_number'],'users_phone_number_deleted_at_unique');
        });
    }
};
