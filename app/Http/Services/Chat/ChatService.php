<?php
declare(strict_types=1);

namespace App\Http\Services\Chat;

use App\Http\Requests\Api\Chat\CloseChatsRequest;
use App\Models\Chat\Chat;

final class ChatService
{
    public function bulkClose(CloseChatsRequest $request): void
    {
        $chats = Chat::query()->with('order')->find($request->chats);
        foreach ($chats as $chat) {
            $this->close($chat);
        }
    }

    public function close(Chat $chat)
    {
        $chat->delete();
    }
}
