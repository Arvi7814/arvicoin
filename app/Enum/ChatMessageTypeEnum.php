<?php
declare(strict_types=1);

namespace App\Enum;

enum ChatMessageTypeEnum: string
{
    case TEXT = 'text';
    case MEDIA = 'media';
}
