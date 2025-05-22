<?php
declare(strict_types=1);

namespace App\Http\Services\User;

use App\Enum\LangEnum;
use App\Http\Requests\Api\User\UpdateProfileRequest;
use App\Models\User\User;

class ProfileService
{
    public function update(UpdateProfileRequest $request, User $user): User
    {
        if ($request->first_name) {
            $user->first_name = $request->first_name;
        }
        if ($request->last_name) {
            $user->last_name = $request->last_name;
        }
        if ($request->language) {
            $user->language = LangEnum::from($request->language);
        }
        if ($request->latitude) {
            $user->latitude = $request->latitude;
        }
        if ($request->longitude) {
            $user->longitude = $request->longitude;
        }
        $user->save();

        return $user;
    }
}
