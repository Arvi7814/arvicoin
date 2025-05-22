<?php
declare(strict_types=1);

namespace App\Telegram\Messages;

use App\Models\Order\Order;
use App\Models\System\Setting;
use App\Telegram\Commands\MessageParams;

final class CheckoutMessage
{
    public static function make(): MessageParams
    {
        return new MessageParams(
            message: Setting::tgOrderMessage()->translation()
        );
    }
}
