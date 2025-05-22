<?php
declare(strict_types=1);

namespace App\Http\Services;

use App\Models\User\User;
use App\Models\User\UserAccessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class ApiAuthService
{
    public function login(string $token): User
    {
        /** @var UserAccessToken $userToken */
        $userToken = UserAccessToken::query()
            ->whereToken($token)
            ->with(['user'])
            ->first();

        if (!$userToken || !$userToken->user) {
            throw new UnauthorizedException();
        }

        Auth::login($userToken->user);

        return $userToken->user;
    }
}
