<?php

namespace App\Jobs\Chat;

use App\Models\Chat\ChatUnreadMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteUnreadMessagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $chatId,
        private readonly int $retriever
    )
    {
    }

    public function handle(): void
    {
        ChatUnreadMessage::query()->where([
            'user_id' => $this->retriever,
            'chat_id' => $this->chatId
        ])->delete();
    }
}
