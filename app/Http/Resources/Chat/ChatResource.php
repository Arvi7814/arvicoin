<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\Order\OrderResource;
use App\Models\Chat\Chat;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Chat $resource
 */
class ChatResource extends JsonResource
{
    /**
     * @param $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $lastMessage = $this->resource->lastMessage();

        return [
            'id' => $this->resource->id,
            'order' => OrderResource::make($this->whenLoaded('order')),
            'last_message' => $lastMessage ? ChatMessageResource::make($lastMessage) : null,
            'unread_chat_messages_count' => $this->whenLoaded('unreadChatMessages', $this->resource->unread_chat_messages_count)
        ];
    }
}
