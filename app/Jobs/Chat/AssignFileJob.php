<?php

namespace App\Jobs\Chat;

use App\Enum\ChatMessageTypeEnum;
use App\Events\Chat\NewMessageEvent;
use App\Integration\TgLogger;
use App\Models\Chat\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

class AssignFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int|string  $fileId,
        private readonly ChatMessage $chatMessage)
    {
    }

    public function handle(TgLogger $logger)
    {
        try {
            $token = env('TELEGRAM_BOT_TOKEN');
            $chatMessage = $this->chatMessage;
            $file = Telegram::bot()->getFile([
                'file_id' => $this->fileId
            ]);

            $chatMessage
                ->addMediaFromUrl(
                    "https://api.telegram.org/file/bot$token/{$file->filePath}"
                )
                ->toMediaCollection();

            $chatMessage->type = ChatMessageTypeEnum::MEDIA;
            $chatMessage->save();

            broadcast(new NewMessageEvent(
                chat: $chatMessage->chat,
                chatMessage: $chatMessage
            ))->toOthers();
        } catch (Throwable $e) {
            $logger->log($e);

            throw $e;
        }
    }
}
