<?php

namespace App\Console\Commands\Sms;

use App\Jobs\Sms\UpdateTokenJob;
use Illuminate\Console\Command;

class UpdateTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:update-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the sms token';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        UpdateTokenJob::dispatch();

        return Command::SUCCESS;
    }
}
