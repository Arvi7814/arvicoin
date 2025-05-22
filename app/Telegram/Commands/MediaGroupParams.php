<?php
declare(strict_types=1);

namespace App\Telegram\Commands;

use Psy\Util\Json;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Telegram\Bot\Objects\InputMedia\InputMediaPhoto;

final class MediaGroupParams
{
    public function __construct(
        public readonly MediaCollection $media
    )
    {
    }

    public function get(float $chatId): array
    {
        $inputPhotos = [];
        $params = [
            'chat_id' => $chatId,
        ];

        foreach ($this->media as $media) {
            $inputPhotos[] = InputMediaPhoto::make([
                'type' => 'photo',
                'media' => $media->getFullUrl()
            ]);
        }

        $params['media'] = Json::encode($inputPhotos);

        return $params;
    }
}
