<?php

declare(strict_types=1);

namespace App\Enum;

enum BannerTypeEnum: string
{
    case NOTIFICATION = 'notification';
    case SLIDE = 'slide';

    public static function options(): array
    {
        return [
            self::NOTIFICATION->value => trans('fields.notification'),
            self::SLIDE->value => trans('fields.slide'),
        ];
    }
}
