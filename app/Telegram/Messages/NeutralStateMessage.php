<?php
declare(strict_types=1);

namespace App\Telegram\Messages;

use App\Models\Shop\Product;
use App\Telegram\Commands\MessageParams;
use Telegram\Bot\Keyboard\Keyboard;

final class NeutralStateMessage
{
    public static function make(): MessageParams
    {
        return new MessageParams(
            message: trans('messages.menu'),
            keyboard: self::keyboard()
        );
    }

    public static function keyboard(): array
    {
        $keyboard = [];

        $products = Product::query()->get()->chunk(2);

        foreach ($products as $items) {
            $list = [];
            foreach ($items as $item) {
                $list[] = Keyboard::button([
                    'text' => $item->name
                ]);
            }
            $keyboard[] = $list;
        }

        $keyboard[] = [
            Keyboard::button([
                'text' => trans('messages.settings')
            ]),
            Keyboard::button([
                'text' => trans('messages.about-us')
            ])
        ];

        return $keyboard;
    }
}
