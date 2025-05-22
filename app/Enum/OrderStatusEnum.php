<?php

namespace App\Enum;

enum OrderStatusEnum: string
{
    case OPENED = 'opened';
    case ACCEPTED = 'accepted';
    case CLOSED = 'closed';

    public static function options(): array
    {
        return [
            self::OPENED->value => trans('fields.opened'),
            self::ACCEPTED->value => trans('fields.accepted'),
            self::CLOSED->value => trans('fields.closed'),
        ];
    }
}
