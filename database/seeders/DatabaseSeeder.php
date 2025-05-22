<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enum\RoleEnum;
use App\Enum\SettingsEnum;
use App\Models\System\Setting;
use App\Models\User\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use function Clue\StreamFilter\fun;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        foreach (RoleEnum::cases() as $role) {
            Role::findOrCreate($role->value);
        }

        foreach (SettingsEnum::cases() as $settingsEnum) {
            Setting::getSetting($settingsEnum);
        }

        User::query()->chunk(20, function ($users) {
            foreach ($users as $user) {
                /** @var User $user */
                $roles = $user->getRoleNames();

                if ($roles->isEmpty()) {
                    $user->assignRole(RoleEnum::CUSTOMER->value);
                }
            }
        });
    }
}
