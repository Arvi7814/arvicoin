<?php
declare(strict_types=1);

namespace App\Telegram\Messages;

use App\Telegram\Helpers\CallbackData;
use Telegram\Bot\Keyboard\Button;
use Telegram\Bot\Keyboard\Keyboard;

final class Navigation
{
    public static function backButton(): Button
    {
        return Keyboard::button([
            'text' => self::backButtonParam(),
        ]);
    }

    public static function inlineBackButton(...$params): Button
    {
        return Keyboard::button([
            'text' => self::backButtonParam(),
            'callback_data' => CallbackData::make(self::backButtonParam(), ...$params)
        ]);
    }

    public static function isBackCallback(array $params): bool
    {
        return $params[0] === self::backButtonParam();
    }

    public static function isBackText(string $text): bool
    {
        return $text === self::backButtonParam();
    }

    private static function backButtonParam(): string
    {
        return trans('messages.back');
    }
}
