<?php
declare(strict_types=1);

namespace App\Telegram\Commands;

final class SendMediaCommand
{
    public function __construct(
        public readonly MediaParams $params,
        public readonly bool        $replaceLastMessage = false,
        public readonly ?string     $nextState = null
    )
    {
    }
}
