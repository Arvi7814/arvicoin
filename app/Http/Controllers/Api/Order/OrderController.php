<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order\Order;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return OrderResource::collection(
            Order::query()
                ->whereUser(Auth::id())
                ->with(['chat', 'operator'])
                ->latest()
                ->paginate()
        );
    }

    public function show(): OrderResource
    {
        return OrderResource::make(
            Order::query()
                ->whereUser(Auth::id())
                ->with([
                    'chat',
                    'operator',
                    'orderProducts',
                    'orderProducts.product'
                ])
                ->first()
        );
    }
}
