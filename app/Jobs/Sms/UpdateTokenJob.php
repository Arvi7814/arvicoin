<?php

namespace App\Jobs\Sms;

use App\Exceptions\Sms\SmsTokenRefreshFailedException;
use App\Integration\EskizService;
use App\Models\System\Setting;
use DragonCode\Contracts\Queue\ShouldBeUnique;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class UpdateTokenJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @throws SmsTokenRefreshFailedException
     */
    public function handle(EskizService $smsService): void
    {
        $response = $smsService->login();

        try {
            $setting = Setting::findSmsGateToken();
            $setting->value = $response['data']['token'];
            $setting->save();

        } catch (Throwable $e) {
            throw new SmsTokenRefreshFailedException($e->getMessage(), $e->getCode());
        }
    }

    public function uniqueId(): string
    {
        return 'update-token-job';
    }

    public function uniqueFor(): int
    {
        return 3600;
    }
}
