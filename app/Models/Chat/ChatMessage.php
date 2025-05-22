<?php

namespace App\Models\Chat;

use App\Enum\ChatMessageTypeEnum;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property ChatMessageTypeEnum $type
 * @property string|null $content
 * @property int $user_id
 * @property int $chat_id
 * @property ?string $tg_message_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * @property-read User $user
 * @property-read Chat $chat
 */
class ChatMessage extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $with = ['media'];
    protected $guarded = [];

    protected $perPage = 15;

    protected $casts = [
        'type' => ChatMessageTypeEnum::class
    ];

    /**
     * @return BelongsTo<User, ChatMessage>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * @return BelongsTo<Chat, ChatMessage>
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }
}
