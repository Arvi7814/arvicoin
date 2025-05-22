<?php

namespace App\Models\Chat;

use App\Models\Order\Order;
use App\Models\Query\ChatQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $order_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read int|null $unread_chat_messages_count
 *
 * @property-read Order $order
 * @property-read Collection<ChatMember> $chatMembers
 * @property-read Collection<ChatMessage> $messages
 * @property-read Collection<ChatUnreadMessage> $unreadChatMessages
 *
 * @method static ChatQuery query()
 */
class Chat extends Model
{
    protected $guarded = [];

    protected $perPage = 15;

    public function newEloquentBuilder($query): ChatQuery
    {
        return new ChatQuery($query);
    }

    public function delete()
    {
        $this->order->delete();
    }

    /**
     * @return BelongsTo<Order, Chat>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * @return HasMany<ChatMember>
     */
    public function chatMembers(): HasMany
    {
        return $this->hasMany(ChatMember::class, 'chat_id');
    }

    /**
     * @return HasMany<ChatMessage>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'chat_id');
    }

    /**
     * @return ChatMessage|null
     */
    public function lastMessage(): ?Model
    {
        return ChatMessage::withoutEvents(function () {
            return ChatMessage::query()
                ->where([
                    'chat_id' => $this->id
                ])
                ->latest()
                ->first();
        });
    }

    /**
     * @return HasMany<ChatUnreadMessage>
     */
    public function unreadChatMessages(): HasMany
    {
        return $this->hasMany(ChatUnreadMessage::class, 'chat_id');
    }
}
