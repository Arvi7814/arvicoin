<?php

namespace App\Events\Chat;

use App\Http\Resources\Chat\ChatMessageResource;
use App\Models\Chat\Chat;
use App\Models\Chat\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $afterCommit = true;

    public function __construct(
        private readonly Chat       $chat,
        public readonly ChatMessage $chatMessage
    )
    {
    }

    public function broadcastOn(): array
    {
        $channels = [new Channel("chats.{$this->chat->id}")];

        foreach ($this->chat->chatMembers as $chatMember) {
            if ($chatMember->user_id !== $this->chatMessage->user_id) {
                $channels[] = new Channel("users.{$chatMember->user_id}");
            }
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chat->id,
            'order_id' => $this->chat->order_id,
            'message' => ChatMessageResource::make($this->chatMessage->load(['user']))
        ];
    }
}
