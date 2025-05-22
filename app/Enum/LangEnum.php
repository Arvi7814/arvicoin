<?php

declare(strict_types=1);

namespace App\Enum;

enum LangEnum: string
{
    case RU = 'ru';
    case UZ = 'uz';
    case EN = 'en';

    public static function default(): LangEnum
    {
        return self::RU;
    }

    public function currency(): CurrencyEnum
    {
        return match ($this) {
            self::RU => CurrencyEnum::RUB,
            self::UZ => CurrencyEnum::UZS,
            self::EN => CurrencyEnum::USD
        };
    }
}
