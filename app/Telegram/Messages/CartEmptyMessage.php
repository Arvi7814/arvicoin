<?php
declare(strict_types=1);

namespace App\Telegram\Messages;

use App\Models\Shop\CartProduct;
use App\Telegram\Commands\MediaParams;
use App\Telegram\Commands\MessageParams;
use App\Telegram\FakeMedia;
use App\Telegram\Helpers\CallbackData;
use Illuminate\Support\Facades\App;
use Telegram\Bot\Keyboard\Keyboard;

final class CartEmptyMessage
{
    public static function makeMessageParams(): MessageParams
    {
        return new MessageParams(
            message: trans('messages.cart-empty'),
            inlineKeyboard: NeutralStateMessage::keyboard()
        );
    }
}
