<?php

declare(strict_types=1);

namespace App\Http\Services\Auth;

use App\Exceptions\Sms\SmsAlreadySentException;
use App\Jobs\Sms\SendMessageJob;
use App\Models\System\SmsConfirmation;

class SmsService
{
    /**
     * @throws SmsAlreadySentException
     */
    public function sendSignupCode(string $phoneNumber, string $code): void
    {
        $this->sendMessage($phoneNumber, $code, SmsConfirmation::SIGNUP_CONFIRM);
    }

    /**
     * @throws SmsAlreadySentException
     */
    public function sendLoginCode(string $phoneNumber, string $code): void
    {
        $this->sendMessage($phoneNumber, $code, SmsConfirmation::LOGIN_CONFIRM);
    }

    /**
     * @throws SmsAlreadySentException
     */
    private function sendMessage(string $phoneNumber, string $code, string $type): void
    {
        $isTestMessage = $phoneNumber === env('DEFAULT_PHONE_NUMBER');

        if ($isTestMessage) {
            $code = env('DEFAULT_CODE');
        } else {
            $this->checkIfAlreadySent($phoneNumber, $type);
        }

        $sms = new SmsConfirmation();
        $sms->code = $code;
        $sms->type = $type;
        $sms->expires_at = now()->addMinutes(2);
        $sms->phone_number = $phoneNumber;
        $sms->ip_address = request()->ip();
        $sms->save();

        if (!$isTestMessage) {
            SendMessageJob::dispatch($sms->phone_number, trans('messages.confirm-code', [
                'code' => $code,
            ]));
        }
    }

    /**
     * @throws SmsAlreadySentException
     */
    private function checkIfAlreadySent(string $phoneNumber, string $type): void
    {
        $isAlreadySent = SmsConfirmation::query()
            ->whereType($type)
            ->wherePhoneNumber($phoneNumber)
            ->active()
            ->exists();

        if ($isAlreadySent) {
            throw new SmsAlreadySentException();
        }
    }
}
