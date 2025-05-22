<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Enum\RoleEnum;
use App\Models\Chat\Chat;
use App\Models\User\User;
use App\Policies\ChatPolicy;
use Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Chat::class => ChatPolicy::class
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function (User $user) {
            if ($user->hasRole(RoleEnum::MANAGER->value)) {
                return true;
            }
        });
    }
}
