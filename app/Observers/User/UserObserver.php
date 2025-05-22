<?php

namespace App\Observers\User;

use App\Enum\DeletedStatusEnum;
use App\Enum\OrderStatusEnum;
use App\Models\User\User;

class UserObserver
{
    public $afterCommit = true;

    public function deleted(User $user): void
    {
        $user->orders()->update([
            'deleted_status' => DeletedStatusEnum::BY_CUSTOMER
        ]);
        $user->servedOrders()->update(([
            'operator_id' => null,
            'status' => OrderStatusEnum::OPENED
        ]));
        $user->memberships()->delete();
    }
}
