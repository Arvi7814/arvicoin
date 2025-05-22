<?php
declare(strict_types=1);

namespace App\Telegram\Commands;

final class SendMessageCommand
{
    public function __construct(
        public readonly MessageParams $params,
        public readonly bool          $replaceLastMessage = false,
        public readonly ?string       $nextState = null
    )
    {
    }
}
