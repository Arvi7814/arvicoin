<?php
declare(strict_types=1);

namespace App\Telegram\Commands;

use Psy\Util\Json;
use Telegram\Bot\Keyboard\Keyboard;

final class MessageParams
{
    public function __construct(
        public readonly string $message,
        public readonly array  $inlineKeyboard = [],
        public readonly array  $keyboard = [],
        public readonly bool   $requestContact = false,
        public readonly bool   $requestLocation = false
    )
    {
    }

    public function get(float $chatId): array
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $this->message,
            'parse_mode' => 'HTML'
        ];

        if (!empty($this->inlineKeyboard)) {
            $params['reply_markup'] = Json::encode([
                'inline_keyboard' => $this->inlineKeyboard
            ]);
        }

        if (!empty($this->keyboard)) {
            $params['reply_markup'] = Json::encode([
                'keyboard' => $this->keyboard,
                'one_time_keyboard' => true,
                'resize_keyboard' => true
            ]);
        }

        if ($this->requestContact) {
            $params['reply_markup'] = Json::encode([
                'keyboard' => [
                    [
                        Keyboard::button([
                            'text' => trans('messages.share'),
                            'request_contact' => true,
                        ])
                    ]
                ],
                'one_time_keyboard' => true,
                'resize_keyboard' => true
            ]);
        }

        if ($this->requestLocation) {
            $params['reply_markup'] = Json::encode([
                'keyboard' => [
                    [
                        Keyboard::button([
                            'text' => trans('messages.send-location'),
                            'request_location' => true,
                        ])
                    ]
                ],
                'one_time_keyboard' => true,
                'resize_keyboard' => true
            ]);
        }

        return $params;
    }
}
