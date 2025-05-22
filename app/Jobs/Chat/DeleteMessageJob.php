<?php

namespace App\Jobs\Chat;

use App\Models\Chat\ChatMessage;
use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly User        $user,
        private readonly ChatMessage $chatMessage
    )
    {
    }

    public function handle(): void
    {
        if ($this->user->chat_id && $this->chatMessage->tg_message_id) {
            $this->user->deleteMessage($this->chatMessage->tg_message_id);
        }
    }
}
