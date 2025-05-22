<?php

namespace App\Console\Commands;

use App\Models\AnnounceSent;
use App\Notifications\User\NewAnnounce;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SendAnnounceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:announce';

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
        AnnounceSent::query()
            ->where('sent', false)
            ->with(['announce', 'user'])
            ->chunk(100, function (Collection $announces) {
                /** @var AnnounceSent $announce */
                foreach ($announces as $announce) {
                    $user = $announce->user;
                    $user->notify(new NewAnnounce($announce->announce));

                    $announce->sent = true;
                    $announce->save();
                }
            });

        return Command::SUCCESS;
    }
}
