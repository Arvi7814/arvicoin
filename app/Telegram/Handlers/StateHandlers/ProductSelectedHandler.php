<?php
declare(strict_types=1);

namespace App\Telegram\Handlers\StateHandlers;

use App\Enum\OrderStatusEnum;
use App\Events\Order\NewOrderEvent;
use App\Exceptions\TelegramException;
use App\Enum\UserState;
use App\Jobs\Shop\NotifyOperatorJob;
use App\Models\Order\Order;
use App\Models\Order\OrderProduct;
use App\Models\Shop\CartProduct;
use App\Models\Shop\Product;
use App\Telegram\Commands\SendMessageCommand;
use App\Telegram\Contracts\StateHandler;
use App\Telegram\Helpers\CallbackData;
use App\Telegram\Messages\CartEmptyMessage;
use App\Telegram\Messages\Navigation;
use App\Telegram\Messages\CheckoutMessage;
use App\Telegram\Messages\ProductMessage;
use App\Telegram\Services\ProductService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class ProductSelectedHandler extends StateHandler
{
    public function handle(): void
    {
        try {
            $params = CallbackData::parse($this->request->callbackData());

            if (count($params) === 2) {
                [$productId, $message] = $params;

                if (is_numeric($message)) {
                    $this->handleProductCountInput((int)$productId, (int)$message);
                    return;
                }
            }

            if (count($params) === 1) {
                if ($params[0] === ProductMessage::CHECKOUT) {
                    $this->handleToCheckoutInput();
                    return;
                }

                if (Navigation::isBackCallback($params)) {
                    $this->handleBackInput();
                    return;
                }
            }

            throw new InvalidArgumentException();
        } catch (TelegramException|InvalidArgumentException $e) {
            $this->backToNeutral();
        }
    }

    private function handleBackInput(): void
    {
        $this->user->clearCart();
        $this->backToNeutral();
    }

    private function handleProductCountInput(int $productId, int $count): void
    {
        $product = $this->getProduct($productId);
        $cartProduct = $this->user->getCartProduct($product);

        if ($count === 0) {
            $cartProduct->delete();
            $this->backToNeutral();
            return;
        }

        if ($count > 0 && $count < 10) {
            $cartProduct->count = $count;
            $cartProduct->update();
        }

        $service = new ProductService();
        $service->sendInfo($this->user, $cartProduct);
    }

    private function handleToCheckoutInput(): void
    {
        $user = $this->user;
        $cart = $user->createdCart();

        if ($cart->cartProducts->isEmpty()) {
            $this->user->sendMessage(
                new SendMessageCommand(
                    params: CartEmptyMessage::makeMessageParams(),
                    nextState: UserState::NEUTRAL
                )
            );
        } else {
            DB::transaction(
                function () use ($cart) {
                    $user = $this->user;

                    $order = new Order();
                    $order->user_id = $cart->user_id;
                    $order->status = OrderStatusEnum::OPENED;
                    $order->from_telegram = true;
                    $order->currency = $user->language->currency();
                    $order->save();

                    $user->current_order_id = $order->id;
                    $user->save();

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

                    $user->sendMessage(
                        new SendMessageCommand(
                            params: CheckoutMessage::make(),
                            replaceLastMessage: true,
                            nextState: UserState::ORDERED
                        )
                    );
                }
            );
        }
    }

    private function getProduct(int $productId): Product
    {
        if ($product = Product::query()->where('id', $productId)->first()) {
            return $product;
        }

        throw new InvalidArgumentException();
    }
}
