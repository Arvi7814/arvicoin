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
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('coin');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('coin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('order_products', function (Blueprint $table) {
            $table->unsignedInteger('coin')->default(0);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('coin')->default(0);
        });
    }
};
