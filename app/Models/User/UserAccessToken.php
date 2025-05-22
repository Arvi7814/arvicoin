<?php

namespace App\Models\User;

use App\Models\Query\UserAccessTokenQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $token
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property User $user
 *
 * @method static UserAccessTokenQuery query()
 */
class UserAccessToken extends Model
{
    protected $guarded = [];

    public function newEloquentBuilder($query): UserAccessTokenQuery
    {
        return new UserAccessTokenQuery($query);
    }

    /**
     * @return BelongsTo<User, UserAccessToken>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
