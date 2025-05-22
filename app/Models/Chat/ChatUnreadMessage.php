<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $chat_id
 * @property int $chat_message_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ChatUnreadMessage extends Model
{
    protected $guarded = [];
}
