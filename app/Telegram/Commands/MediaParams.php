<?php
declare(strict_types=1);

namespace App\Telegram\Commands;

use App\Helpers\MediaHelper;
use Illuminate\Support\Facades\Storage;
use Psy\Util\Json;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;

final class MediaParams
{
    public function __construct(
        public readonly Media  $media,
        public readonly string $message,
        public readonly array  $inlineKeyboard = [],
        public readonly array  $keyboard = [],
        public readonly bool   $requestContact = false
    )
    {
    }

    public function get(float $chatId): array
    {
        $params = [
            'chat_id' => $chatId,
            'parse_mode' => 'HTML'
        ];

        if ($this->message) {
            $params['caption'] = $this->message;
        }

        if ($this->isPhoto()) {
            $key = 'photo';
        } else if ($this->isVideo()) {
            $key = 'video';
        } else if ($this->isAnimation()) {
            $key = 'animation';
        } else if ($this->isAudio()) {
            $key = 'audio';
        } else {
            $key = 'document';
        }

        $params[$key] = InputFile::create(
            Storage::readStream($this->media->getPath()),
            $this->media->name
        );

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

        return $params;
    }

    public function isPhoto(): bool
    {
        return MediaHelper::isPhoto($this->media);
    }

    public function isVideo(): bool
    {
        return MediaHelper::isVideo($this->media);
    }

    public function isAnimation(): bool
    {
        return MediaHelper::isAnimation($this->media);
    }

    public function isAudio(): bool
    {
        return MediaHelper::isAudio($this->media);
    }
}
