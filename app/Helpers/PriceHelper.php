<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Enum\CurrencyEnum;
use Filament\Forms\Components\KeyValue;

class PriceHelper
{
    public static function input(string $name): KeyValue
    {
        return KeyValue::make($name)
            ->default(self::default())
            ->disableAddingRows()
            ->disableDeletingRows()
            ->disableEditingKeys();
    }
    
    /**
     * @return array<string, string>
     */
    public static function default(): array
    {
        $options = [];

        foreach (CurrencyEnum::cases() as $currencyEnum) {
            $options[$currencyEnum->value] = 0;
        }

        return $options;
    }
}
