<?php

declare(strict_types=1);

namespace App\Http\Services\User;

use App\Models\User\User;

class FcmService
{
    public function register(User $user, string $token): void
    {
        $user->fcmRegIds()
            ->firstOrCreate([
                'token' => $token
            ]);
    }
}
