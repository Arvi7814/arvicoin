<?php

namespace App\Console\Commands;

use App\Models\User\User;
use Illuminate\Console\Command;

class GenerateTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:token';

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
        $phoneNumber = $this->ask('Phone number');

        /** @var User $user */
        $user = User::query()
            ->phoneNumber($phoneNumber)
            ->first();

        if ($user) {
            $this->info($user->getAccessToken()->token);
        }

        return Command::SUCCESS;
    }
}
