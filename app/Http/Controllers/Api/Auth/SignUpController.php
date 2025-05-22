<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\Sms\SmsAlreadySentException;
use App\Exceptions\Sms\WrongCodeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\Register\RegisterRequest;
use App\Http\Requests\Api\Auth\Register\SendCodeRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Services\Auth\RegisterService;
use http\Exception\BadMethodCallException;

class SignUpController extends Controller
{
    public function __construct(
        private readonly RegisterService $registerService
    )
    {
    }

    /**
     * @throws SmsAlreadySentException
     */
    public function sendCode(SendCodeRequest $request)
    {
        $this->registerService->sendCode($request);

        return response()->json();
    }

    /**
     * @throws WrongCodeException
     */
    public function register(RegisterRequest $request)
    {
        $ip = request()->ip();
        if (!$ip) {
            throw new BadMethodCallException();
        }

        $user = $this->registerService->register($request);

        return response()->json([
            'user' => UserResource::make($user),
            'token' => $user->getAccessToken($ip)->token,
        ]);
    }
}
