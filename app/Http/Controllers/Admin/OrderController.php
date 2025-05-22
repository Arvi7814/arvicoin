<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Services\Order\OrderService;
use App\Models\Order\Order;
use Illuminate\Http\RedirectResponse;

final class OrderController
{
    public function __construct(
        private readonly OrderService $orderService,
    )
    {
    }

    public function close(Order $order): RedirectResponse
    {
        $this->orderService->deleteOrder($order);

        return redirect()->route('filament.resources.order/orders.index');
    }
}
