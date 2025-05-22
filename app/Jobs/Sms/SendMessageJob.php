<?php

namespace App\Jobs\Sms;

use App\Integration\EskizService;
use App\Integration\PilotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $phoneNumber,
        private readonly string $text,
    )
    {
    }

    public function handle(PilotService $smsService)
    {
        $smsService->sendMessage(
            $this->phoneNumber,
            $this->text
        );
    }
}
