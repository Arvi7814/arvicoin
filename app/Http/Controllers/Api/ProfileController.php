<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enum\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UpdateProfileRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Services\User\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ProfileController extends Controller
{
    public function me(): JsonResponse
    {
        if ($user = Auth::user()) {
            return response()->json(
                UserResource::make($user)
            );
        }

        throw new UnauthorizedHttpException();
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $service = new ProfileService();
        return response()->json(
            UserResource::make($service
                ->update(
                    $request,
                    Auth::user()
                )
            )
        );
    }

    public function delete(): \Illuminate\Http\Response
    {
        if ($user = Auth::user()) {
            $user->status = UserStatus::DELETED;
            $user->save();
        }

        return Response::noContent();
    }
}
