<?php
declare(strict_types=1);

namespace App\Telegram\Messages;

use App\Telegram\Commands\MessageParams;
use Telegram\Bot\Keyboard\Keyboard;

final class SettingsMessage
{
    public static function make(): MessageParams
    {
        return new MessageParams(
            message: trans('messages.settings'),
            keyboard: [
                [
                    Keyboard::button([
                        'text' => trans('messages.change-lang')
                    ])
                ],
                [
                    Navigation::backButton()
                ]
            ]
        );
    }
}
