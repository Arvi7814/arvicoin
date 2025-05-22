<?php

namespace App\Jobs\User;

use App\Models\Announce;
use App\Models\AnnounceSent;
use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SendAnnounceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $announceId
    )
    {
    }

    public function handle(): void
    {
        if (Announce::query()->whereKey($this->announceId)->exists()) {
            AnnounceSent::query()->where('announce_id', $this->announceId)->delete();
            
            User::query()->chunk(100, function (Collection $users) {
                foreach ($users as $user) {
                    AnnounceSent::query()->firstOrCreate([
                        'user_id' => $user->id,
                        'announce_id' => $this->announceId
                    ]);
                }
            });
        }
    }
}
