<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\Auth\UserDoesNotExist;
use App\Exceptions\Sms\SmsAlreadySentException;
use App\Exceptions\Sms\WrongCodeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\Login\LoginRequest;
use App\Http\Requests\Api\Auth\Login\SendCodeRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Services\Auth\LoginService;
use BadMethodCallException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    public function __construct(
        private readonly LoginService $loginService
    )
    {
    }

    /**
     * @throws UserDoesNotExist
     * @throws SmsAlreadySentException
     */
    public function sendCode(SendCodeRequest $request): Response
    {
        $this->loginService->sendCode($request);

        return response()->noContent();
    }

    /**
     * @throws WrongCodeException
     * @throws UserDoesNotExist
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $ip = $request->ip();
        if (!$ip) {
            throw new BadMethodCallException();
        }

        $user = $this->loginService->login($request);

        return response()->json([
            'user' => UserResource::make($user),
            'token' => $user->getAccessToken($ip)->token,
        ]);
    }
}
