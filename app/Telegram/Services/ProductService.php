<?php
declare(strict_types=1);

namespace App\Telegram\Services;

use App\Enum\UserState;
use App\Models\Shop\CartProduct;
use App\Models\Shop\Product;
use App\Models\User\User;
use App\Telegram\Commands\SendMediaCommand;
use App\Telegram\Commands\SendMessageCommand;
use App\Telegram\Messages\ProductMessage;

final class ProductService
{
    public function sendInfo(User $user, CartProduct $cartProduct): void
    {
        $product = $cartProduct->product;

        if($media = $product->getFirstMedia('coverage')) {
            $user->sendMedia(
                new SendMediaCommand(
                    params: ProductMessage::makeMediaParams($user, $cartProduct, $media),
                    nextState: UserState::PRODUCT_SELECTED
                )
            );
        } else {
            $user->sendMessage(
                new SendMessageCommand(
                    params: ProductMessage::makeMessageParams($user, $cartProduct),
                    nextState: UserState::PRODUCT_SELECTED
                )
            );
        }
    }
}
