<?php

declare(strict_types=1);

namespace App\Models\Query;

use App\Enum\RoleEnum;
use App\Enum\UserStatus;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<User>
 *
 * @method User|null first($columns = ['*'])
 */
class UserQuery extends Builder
{
    public function whereAvailable(): self
    {
        return $this->whereIn('status', [
            UserStatus::ACTIVE,
            UserStatus::DISABLED
        ]);
    }

    public function phoneNumber(string $phoneNumber): self
    {
        return $this->where('phone_number', $phoneNumber);
    }

    public function customers(): self
    {
        return $this->whereHas(
            'roles',
            fn(Builder $query) => $query->where('name', RoleEnum::CUSTOMER->value)
        );
    }
}
