<?php

declare(strict_types=1);

namespace App\Helpers;

use Filament\Forms\Components\TextInput;

class PhoneNumberHelper
{
    public static function regex(): string
    {
        return '/^\+998([378]{2}|(9[013-57-9]))\d{7}$/i';
    }

    public static function mask(TextInput\Mask $mask): TextInput\Mask
    {
        return $mask->pattern('{+998}(00)000-00-00');
    }
}
