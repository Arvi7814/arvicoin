<?php

declare(strict_types=1);

namespace App\Http\Services\Auth;

use App\Enum\RoleEnum;
use App\Exceptions\Sms\SmsAlreadySentException;
use App\Exceptions\Sms\WrongCodeException;
use App\Http\Requests\Api\Auth\Register\RegisterRequest;
use App\Http\Requests\Api\Auth\Register\SendCodeRequest;
use App\Http\Services\Tokenizer;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterService
{
    public function __construct(
        private readonly SmsService $smsService,
        private readonly Tokenizer  $tokenizer
    )
    {
    }

    /**
     * @param SendCodeRequest $request
     * @return void
     *
     * @throws SmsAlreadySentException
     */
    public function sendCode(SendCodeRequest $request): void
    {
        $this->smsService->sendSignupCode(
            $request->phone_number,
            $this->tokenizer->smsCode()
        );
    }

    /**
     * @param RegisterRequest $request
     * @return User
     *
     * @throws WrongCodeException
     */
    public function register(RegisterRequest $request): User
    {
//        $confirmed = SmsConfirmation::query()
//            ->whereCode($request->code)
//            ->whereType(SmsConfirmation::SIGNUP_CONFIRM)
//            ->wherePhoneNumber($request->phone_number)
//            ->active()
//            ->exists();
//
//        if (!$confirmed) {
//            throw new WrongCodeException();
//        }

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone_number = $request->phone_number;
        $user->password = Hash::make(Str::random());
        $user->save();

        $user->assignRole(RoleEnum::CUSTOMER->value);

        return $user;
    }
}
