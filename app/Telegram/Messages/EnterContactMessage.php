<?php
declare(strict_types=1);

namespace App\Telegram\Messages;

use App\Telegram\Commands\MessageParams;

final class EnterContactMessage
{
    public static function make(): MessageParams
    {
        return new MessageParams(
            message: trans('messages.enter-contact'),
            requestContact: true
        );
    }
}
