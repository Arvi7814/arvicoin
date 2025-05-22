<?php

namespace App\Events\Chat;

use App\Models\Chat\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderClosedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $afterCommit = true;

    public function __construct(
        private readonly Chat $chat
    )
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel("chats.{$this->chat->id}");
    }

    public function broadcastAs(): string
    {
        return 'order.closed';
    }
}
