<?php
declare(strict_types=1);

namespace App\Telegram\Messages;

use App\Enum\LangEnum;
use App\Telegram\Commands\MessageParams;
use Telegram\Bot\Keyboard\Keyboard;

final class ChooseLangMessage
{
    public static function make(): MessageParams
    {
        return new MessageParams(
            message: trans('messages.choose-lang'),
            keyboard: self::keyboard()
        );
    }

    public static function keyboard(): array
    {
        return [
            array_map(
                function (string $text) {
                    return Keyboard::button([
                        'text' => $text
                    ]);
                },
                array_keys(self::localeOptions())
            )
        ];
    }

    public static function localeOptions(): array
    {
        $options = [];

        foreach (LangEnum::cases() as $lang) {
            $options[trans("messages.locale.{$lang->value}")] = $lang->value;
        }

        return $options;
    }
}
