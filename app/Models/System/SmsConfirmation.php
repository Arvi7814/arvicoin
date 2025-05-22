<?php

namespace App\Models\System;

use App\Models\Query\SmsConfirmationQuery;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property string $type
 * @property string $phone_number
 * @property ?string $ip_address
 * @property int $user_id
 * @property Carbon $expires_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static SmsConfirmationQuery query()
 */
class SmsConfirmation extends Model
{
    public const SIGNUP_CONFIRM = 'signup-confirm';

    public const LOGIN_CONFIRM = 'login-confirm';

    protected $guarded = [];

    public function newEloquentBuilder($query): SmsConfirmationQuery
    {
        return new SmsConfirmationQuery($query);
    }

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
