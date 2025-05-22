<?php
declare(strict_types=1);

namespace App\Models\Query;

use App\Models\Chat\Chat;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Chat>
 */
class ChatQuery extends Builder
{
    public function whereMember(int $userId): self
    {
        return $this->whereHas(
            'chatMembers',
            fn(Builder $query) => $query->where('user_id', $userId)
        );
    }

    public function active(): self
    {
        return $this->withWhereHas('order', fn($query) => $query->whereNull('deleted_status'));
    }

    public function unread(int $userId): self
    {
        return $this->whereHas(
            'unreadChatMessages',
            fn(Builder $query) => $query->where('user_id', $userId)
        );
    }

    public function withUnreadMessagesCount(int $userId): self
    {
        return $this->withCount([
            'unreadChatMessages' => fn(Builder $query) => $query->where('user_id', $userId)
        ]);
    }
}
