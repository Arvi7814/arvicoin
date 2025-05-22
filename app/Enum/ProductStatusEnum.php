<?php

declare(strict_types=1);

namespace App\Enum;

enum ProductStatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    /**
     * @return array<string, mixed>
     */
    public static function options(): array
    {
        return [
            self::ACTIVE->value => trans('fields.active'),
            self::INACTIVE->value => trans('fields.inactive'),
        ];
    }
}
