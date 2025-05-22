<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Services\User\FcmService;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

final class FcmController extends Controller
{
    public function __construct(
        private readonly FcmService $fcmService
    ) {}

    public function register(FormRequest $request)
    {
        $user = Auth::user();

        if(!$user) {
            return response()->noContent();
        }

        $this->fcmService->register($user, (string)$request->input('token'));

        return response()->noContent();
    }
}
