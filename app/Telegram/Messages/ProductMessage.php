<?php
declare(strict_types=1);

namespace App\Telegram\Messages;

use App\Enum\LangEnum;
use App\Models\Shop\CartProduct;
use App\Models\User\User;
use App\Telegram\Commands\MediaParams;
use App\Telegram\Commands\MessageParams;
use App\Telegram\FakeMedia;
use App\Telegram\Helpers\CallbackData;
use Illuminate\Support\Facades\App;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Telegram\Bot\Keyboard\Keyboard;

final class ProductMessage
{
    const CHECKOUT = 'checkout';

    public static function makeMessageParams(User $user, CartProduct $cartProduct): MessageParams
    {
        $product = $cartProduct->product;

        $message = [
            "<b>{$product->name}</b>"
        ];

        $lang = $user->language ?? LangEnum::RU;
        $currency = $lang->currency();
        $price = $product->prices[$currency->value] ?? null;

        if (null !== $price) {
            $price = $cartProduct->count * $price;
            $message[] = '<b>' . trans('messages.price') . ':</b>' . "$price {$currency->value}";
        }

        return new MessageParams(
            message: join(PHP_EOL, $message),
            inlineKeyboard: [
                [
                    Keyboard::inlineButton([
                        'text' => trans('messages.sub'),
                        'callback_data' => CallbackData::make($cartProduct->product_id, $cartProduct->count - 1)
                    ]),
                    Keyboard::inlineButton([
                        'text' => $cartProduct->count,
                        'callback_data' => CallbackData::make($cartProduct->product_id, $cartProduct->count)
                    ]),
                    Keyboard::inlineButton([
                        'text' => trans('messages.add'),
                        'callback_data' => CallbackData::make($cartProduct->product_id, $cartProduct->count + 1)
                    ])
                ],
                [
                    Navigation::inlineBackButton(),
                    Keyboard::inlineButton([
                        'text' => trans('messages.checkout'),
                        'callback_data' => CallbackData::make(self::CHECKOUT)
                    ])
                ]
            ]
        );
    }

    public static function makeMediaParams(User $user, CartProduct $cartProduct, Media $media): MediaParams
    {
        $params = self::makeMessageParams($user, $cartProduct);

        return new MediaParams(
            media: App::isProduction()
                ? $media
                : new FakeMedia(),
            message: $params->message,
            inlineKeyboard: $params->inlineKeyboard
        );
    }
}
