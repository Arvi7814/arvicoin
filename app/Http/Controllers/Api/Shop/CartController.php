<?php

namespace App\Http\Controllers\Api\Shop;

use App\Exceptions\Order\EmptyCartException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Shop\AddProductRequest;
use App\Http\Requests\Api\Shop\OrderRequest;
use App\Http\Requests\Api\Shop\SubProductRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Shop\CartResource;
use App\Http\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    )
    {
    }

    public function index(): JsonResponse
    {
        $user = Auth::user();

        return response()->json(
            CartResource::make(
                $user->createdCart()->load([
                    'cartProducts',
                    'cartProducts.product'
                ])
            )
        );
    }

    public function addProduct(AddProductRequest $request): JsonResponse
    {
        $user = Auth::user();
        $cart = $this->cartService->addProduct($user, $request);

        return response()->json(
            CartResource::make($cart->load([
                'cartProducts',
                'cartProducts.product'
            ]))
        );
    }

    public function subProduct(SubProductRequest $request): JsonResponse
    {
        $user = Auth::user();
        $cart = $this->cartService->subProduct($user, $request);

        return response()->json(
            CartResource::make($cart->load([
                'cartProducts',
                'cartProducts.product'
            ]))
        );
    }

    public function removeProduct(int $product): JsonResponse
    {
        $user = Auth::user();
        $cart = $this->cartService->removeProduct($user, $product);

        return response()->json(
            CartResource::make($cart->load([
                'cartProducts',
                'cartProducts.product'
            ]))
        );
    }

    /**
     * @throws EmptyCartException
     */
    public function createOrder(OrderRequest $request): JsonResponse
    {
        $user = Auth::user();
        $order = $this->cartService->createOrder($user, $request);

        return response()->json(
            OrderResource::make($order->load([
                'orderProducts',
                'orderProducts.product'
            ]))
        );
    }
}
