<?php

namespace App\Jobs\Chat;

use App\Models\Chat\ChatMessage;
use App\Models\Chat\ChatUnreadMessage;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateUnreadMessagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $chatMessageId
    )
    {

    }

    public function handle(): void
    {
        if ($chatMessage = ChatMessage::withoutEvents(
            function () {
                return ChatMessage::query()->find($this->chatMessageId);
            }
        )) {
            DB::transaction(
                function () use ($chatMessage) {
                    foreach ($chatMessage->chat->chatMembers as $chatMember) {
                        if ($chatMember->user_id === $chatMessage->user_id) continue;

                        $unreadMessage = new ChatUnreadMessage();
                        $unreadMessage->user_id = $chatMember->user_id;
                        $unreadMessage->chat_id = $chatMessage->chat_id;
                        $unreadMessage->chat_message_id = $chatMessage->id;
                        $unreadMessage->save();
                    }
                }
            );
        }
    }
}
