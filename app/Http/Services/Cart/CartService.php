<?php

namespace App\Http\Services\Cart;

use App\Enum\OrderStatusEnum;
use App\Events\Order\NewOrderEvent;
use App\Exceptions\Order\EmptyCartException;
use App\Http\Requests\Api\Shop\AddProductRequest;
use App\Http\Requests\Api\Shop\OrderRequest;
use App\Http\Requests\Api\Shop\SubProductRequest;
use App\Jobs\Shop\NotifyOperatorJob;
use App\Models\Order\Order;
use App\Models\Order\OrderProduct;
use App\Models\Shop\Cart;
use App\Models\Shop\CartProduct;
use App\Models\User\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function addProduct(User $user, AddProductRequest $request): Cart
    {
        return $this->changeProductCount(
            $user,
            (int)$request->product,
            (int)$request->count
        );
    }

    public function subProduct(User $user, SubProductRequest $request): Cart
    {
        return $this->changeProductCount(
            $user,
            (int)$request->product,
            -1 * (int)$request->count
        );
    }

    public function removeProduct(User $user, int $product): Cart
    {
        $cart = $user->createdCart();

        /** @var CartProduct $cartProduct */
        $cartProduct = $cart->cartProducts()->firstOrCreate([
            'product_id' => $product
        ]);
        $cartProduct->delete();

        return $cart;
    }

    private function changeProductCount(User $user, int $product, int $count): Cart
    {
        $cart = $user->createdCart();

        /** @var CartProduct $cartProduct */
        $cartProduct = $cart->cartProducts()->firstOrCreate([
            'product_id' => $product
        ]);

        $cartProduct->count += $count;
        if ($cartProduct->count <= 0) {
            $cartProduct->delete();
        } else {
            $cartProduct->save();
        }

        return $cart;
    }

    /**
     * @throws EmptyCartException
     */
    public function createOrder(User $user, OrderRequest $request): Order
    {
        $cart = $user->createdCart();

        if ($cart->cartProducts->isEmpty()) {
            throw new EmptyCartException();
        }

        return DB::transaction(
            function () use ($cart, $request) {
                $order = new Order();
                $order->user_id = $cart->user_id;
                $order->status = OrderStatusEnum::OPENED;
                $order->tiktok_login = $request->tiktok_login;
                $order->tiktok_password = $request->tiktok_password;
                $order->pubg_id = $request->pubg_id;
                $order->currency = $request->currency;
                $order->save();

                /** @var CartProduct[] $cartProducts */
                $cartProducts = $cart->cartProducts()->with(['product'])->get();
                foreach ($cartProducts as $cartProduct) {
                    if ($cartProduct->product) {
                        $orderProduct = new OrderProduct();
                        $orderProduct->order_id = $order->id;
                        $orderProduct->product_id = $cartProduct->product_id;
                        $orderProduct->count = $cartProduct->count;
                        $orderProduct->prices = $cartProduct->product->prices;
                        $orderProduct->save();
                    }

                    $cartProduct->delete();
                }

                broadcast(new NewOrderEvent())->toOthers();

                NotifyOperatorJob::dispatch($order);

                return $order;
            }
        );
    }
}
