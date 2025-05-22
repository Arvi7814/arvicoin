<?php
declare(strict_types=1);

namespace App\Enum;

enum CurrencyEnum: string
{
    case UZS = 'UZS';
    case RUB = 'RUB';
    case USD = 'USD';

    /**
     * @return array<string, mixed>
     */
    public static function options(): array
    {
        return [
            self::UZS->value => trans('fields.currency.uzs'),
            self::RUB->value => trans('fields.currency.rub'),
            self::USD->value => trans('fields.currency.usd'),
        ];
    }
}
