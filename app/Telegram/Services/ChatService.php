<?php
declare(strict_types=1);

namespace App\Telegram\Services;

use App\Models\Chat\ChatMessage;
use App\Models\User\User;
use App\Telegram\Commands\MediaParams;
use App\Telegram\Commands\MessageParams;
use App\Telegram\Commands\SendMediaCommand;
use App\Telegram\Commands\SendMessageCommand;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class ChatService
{
    public function sendMessage(User $user, ChatMessage $chatMessage): void
    {
        if ($media = $chatMessage->getFirstMedia()) {
            $user->sendMedia(
                new SendMediaCommand(
                    params: $this->mediaMessage($media, (string)$chatMessage->content)
                )
            );

            $chatMessage->tg_message_id = $user->last_message;
            $chatMessage->save();
        } else if ($chatMessage->content) {
            $user->sendMessage(
                new SendMessageCommand(
                    params: $this->textMessage($chatMessage->content)
                )
            );

            $chatMessage->tg_message_id = $user->last_message;
            $chatMessage->save();
        }
    }

    private function textMessage(string $text): MessageParams
    {
        return new MessageParams(
            message: $text
        );
    }

    private function mediaMessage(Media $media, string $text): MediaParams
    {
        return new MediaParams(
            media: $media,
            message: $text
        );
    }
}
