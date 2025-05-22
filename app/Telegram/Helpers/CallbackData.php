<?php
declare(strict_types=1);

namespace App\Telegram\Helpers;

final class CallbackData
{
    public const DELIMITER = '__';

    public static function make(string|int ...$values): string
    {
        return join(self::DELIMITER, $values);
    }

    public static function parse(string $data): array
    {
        return explode(self::DELIMITER, $data);
    }
}
