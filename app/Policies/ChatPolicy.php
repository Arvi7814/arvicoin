<?php

namespace App\Policies;

use App\Enum\RoleEnum;
use App\Models\Chat\Chat;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            RoleEnum::MANAGER->value,
            RoleEnum::MODERATOR->value,
        ]);
    }


    public function view(User $user, Chat $chat): bool
    {
        return $chat->chatMembers()->where('user_id', $user->id)->exists();
    }


    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            RoleEnum::MANAGER->value,
            RoleEnum::CUSTOMER->value
        ]);
    }

    public function update(User $user, Chat $chat): bool
    {
        return $chat->chatMembers()->where('user_id', $user->id)->exists();
    }

    public function delete(User $user, Chat $chat): bool
    {
        return $chat->chatMembers()->where('user_id', $user->id)->exists();
    }

    public function restore(User $user, Chat $chat): bool
    {
        return $chat->chatMembers()->where('user_id', $user->id)->exists();
    }

    public function forceDelete(User $user, Chat $chat): bool
    {
        return $chat->chatMembers()->where('user_id', $user->id)->exists();
    }

    private function isChatMember(User $user, Chat $chat): bool
    {
        return $chat->chatMembers()->where('user_id', $user->id)->exists();
    }
}
