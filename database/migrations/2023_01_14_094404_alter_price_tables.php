<?php

use App\Helpers\PriceHelper;
use App\Models\Order\OrderProduct;
use App\Models\Shop\Product;
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
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('price', 'coin');
            $table->json('prices');
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->renameColumn('price', 'coin');
            $table->json('prices');
        });

        Product::query()->update([
            'prices' => json_encode(PriceHelper::default())
        ]);

        OrderProduct::query()->update([
            'prices' => json_encode(PriceHelper::default())
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('coin', 'price');
            $table->dropColumn('prices');
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->renameColumn('coin', 'price');
            $table->dropColumn('prices');
        });
    }
};
