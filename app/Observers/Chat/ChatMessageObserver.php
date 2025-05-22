<?php

namespace App\Observers\Chat;

use App\Jobs\Chat\CreateUnreadMessagesJob;
use App\Models\Chat\ChatMessage;

class ChatMessageObserver
{
    public $afterCommit = true;

    public function created(ChatMessage $chatMessage): void
    {
        CreateUnreadMessagesJob::dispatch($chatMessage->id);
    }
}
