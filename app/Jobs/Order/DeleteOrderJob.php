<?php

namespace App\Jobs\Order;

use App\Enum\DeletedStatusEnum;
use App\Http\Services\Order\OrderService;
use App\Models\Order\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteOrderJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $id
    )
    {
    }

    public function handle():void
    {
        /** @var ?Order $order */
        $order = Order::query()->find($this->id);

        if($order) {
            $order->deleted_status = DeletedStatusEnum::BY_MANAGER;
            $order->closed_at = now();
            $order->save();
        }
    }

    public function uniqueId(): string {
        return (string)$this->id;
    }
}
