<?php

use App\Enum\DeletedStatusEnum;
use App\Models\Order\Order;
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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('deleted_status')->nullable();
        });

        foreach (Order::query()->with('chat')->get() as $order) {
            /** @var Order $order */
            if ($order->deleted_at) {
                $order->deleted_status = DeletedStatusEnum::BY_MANAGER;
                $order->save();
            } else if (!$order->chat) {
                $order->deleted_status = DeletedStatusEnum::BY_CUSTOMER;
                $order->save();
            } else if ($order->chat->deleted_at) {
                $order->deleted_status = DeletedStatusEnum::BY_CUSTOMER;
                $order->save();
            }
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('deleted_status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
};
