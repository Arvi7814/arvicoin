<?php
declare(strict_types=1);

namespace App\Enum;

enum UserStatus: int
{
    case ACTIVE = 1;
    case DISABLED = 2;
    case DELETED = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => trans('fields.active'),
            self::DISABLED => trans('fields.inactive'),
            self::DELETED => trans('fields.deleted'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::DISABLED => 'gray',
            self::DELETED => 'danger'
        };
    }
}
