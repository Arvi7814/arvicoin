<?php

declare(strict_types=1);

namespace App\Http\Services\Auth;

use App\Exceptions\Auth\UserDoesNotExist;
use App\Exceptions\Sms\SmsAlreadySentException;
use App\Exceptions\Sms\WrongCodeException;
use App\Http\Requests\Api\Auth\Login\LoginRequest;
use App\Http\Requests\Api\Auth\Login\SendCodeRequest;
use App\Http\Services\Tokenizer;
use App\Models\User\User;

class LoginService
{
    public function __construct(
        private readonly SmsService $smsService,
        private readonly Tokenizer  $tokenizer
    )
    {
    }

    /**
     * @throws SmsAlreadySentException
     * @throws UserDoesNotExist
     */
    public function sendCode(SendCodeRequest $request): void
    {
        $user = $this->findUser($request->phone_number);

        $this->smsService->sendLoginCode(
            $user->phone_number,
            $this->tokenizer->smsCode()
        );
    }

    /**
     * @param LoginRequest $request
     * @return User
     *
     * @throws WrongCodeException
     * @throws UserDoesNotExist
     */
    public function login(LoginRequest $request): User
    {
//        $confirmed = SmsConfirmation::query()
//            ->whereCode($request->code)
//            ->whereType(SmsConfirmation::LOGIN_CONFIRM)
//            ->wherePhoneNumber($request->phone_number)
//            ->active()
//            ->exists();
//
//        if (!$confirmed) {
//            throw new WrongCodeException();
//        }

        return $this->findUser($request->phone_number);
    }

    /**
     * @throws UserDoesNotExist
     */
    private function findUser(string $phoneNumber): User
    {
        $user = User::query()
            ->whereAvailable()
            ->phoneNumber($phoneNumber)
            ->first();

        if (!$user) {
            throw new UserDoesNotExist();
        }

        return $user;
    }
}
