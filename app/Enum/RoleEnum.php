<?php

declare(strict_types=1);

namespace App\Enum;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case MODERATOR = 'moderator';
    case CUSTOMER = 'customer';
}
