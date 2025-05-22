<?php

namespace App\Models;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property boolean $sent
 * @property int $announce_id
 * @property int $user_id
 *
 * @property User $user
 * @property Announce $announce
 */
class AnnounceSent extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function announce(): BelongsTo
    {
        return $this->belongsTo(Announce::class, 'announce_id');
    }
}
