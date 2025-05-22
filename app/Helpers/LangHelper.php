<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Enum\LangEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;

class LangHelper
{
    public static function input(string $name): KeyValue
    {
        return KeyValue::make($name)
            ->default(self::default())
            ->disableAddingRows()
            ->disableDeletingRows()
            ->disableEditingKeys();
    }

    public static function tabs(string $name): Tabs
    {
        $tabs = [];

        foreach (LangEnum::cases() as $lang) {
            $tabs[] = Tabs\Tab::make($lang->value)
                ->schema([
                    Textarea::make("{$name}.{$lang->value}")
                        ->default('')
                        ->required()
                ]);
        }

        return Tabs::make($name)
            ->tabs($tabs);
    }

    /**
     * @return array<string, string>
     */
    public static function default(): array
    {
        $options = [];

        foreach (LangEnum::cases() as $langEnum) {
            $options[$langEnum->value] = '';
        }

        return $options;
    }
}
