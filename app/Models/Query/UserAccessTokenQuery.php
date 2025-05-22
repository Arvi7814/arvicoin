<?php
declare(strict_types=1);

namespace App\Models\Query;

use App\Models\User\UserAccessToken;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<UserAccessToken>
 */
class UserAccessTokenQuery extends Builder
{
    public function whereToken(string $token): self
    {
        return $this->where('token', $token);
    }
}
