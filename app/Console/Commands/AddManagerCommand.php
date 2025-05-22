<?php

namespace App\Console\Commands;

use App\Enum\RoleEnum;
use App\Models\User\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class AddManagerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new manager';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $firstName = $this->ask('Firstname');
        $lastName = $this->ask('Lastname');
        $phoneNumber = $this->ask('Phone number');
        $password = $this->secret('Password');

        $user = new User();
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->phone_number = $phoneNumber;
        $user->password = Hash::make($password);
        $user->save();

        $user->assignRole(RoleEnum::MANAGER->value);

        return Command::SUCCESS;
    }
}
