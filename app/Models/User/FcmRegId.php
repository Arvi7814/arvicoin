<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $token
 */
class FcmRegId extends Model
{
    protected $guarded = [];
}
