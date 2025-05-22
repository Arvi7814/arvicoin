<?php

declare(strict_types=1);

namespace App\Enum;

enum DeletedStatusEnum: string
{
    case BY_CUSTOMER = 'by-customer';
    case BY_MODERATOR = 'by-moderator';
    case BY_MANAGER = 'by-manager';
}
