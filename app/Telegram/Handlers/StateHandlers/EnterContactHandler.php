<?php
declare(strict_types=1);

namespace App\Telegram\Handlers\StateHandlers;

use App\Enum\RoleEnum;
use App\Exceptions\TelegramException;
use App\Enum\UserState;
use App\Models\User\User;
use App\Telegram\Commands\MessageParams;
use App\Telegram\Commands\SendMessageCommand;
use App\Telegram\Contracts\StateHandler;
use App\Telegram\Messages\EnterContactMessage;
use App\Telegram\Messages\NeutralStateMessage;
use Illuminate\Support\Facades\DB;

final class EnterContactHandler extends StateHandler
{
    public function handle(): void
    {
        try {
            $contact = $this->request->contact();
            $this->savePhoneNumber($contact->phoneNumber);
            $this->sendMenu();
            return;
        } catch (TelegramException $e) {

        }

        $this->askCorrectData(
            params: EnterContactMessage::make()
        );
    }

    private function savePhoneNumber(string $phoneNumber): void
    {
        $tgUser = $this->user;
        if ($user = User::query()->phoneNumber($phoneNumber)->first()) {
            DB::transaction(function () use ($tgUser, $user) {
                /** @var User $user */
                $user->chat_id = $tgUser->chat_id;
                $user->username = $tgUser->username;
                $user->language = $tgUser->language;
                $user->save();

                $tgUser->delete();

                $this->user = $user;
            });
        } else {
            $tgUser->phone_number = $phoneNumber;
            $tgUser->save();

            $tgUser->assignRole(RoleEnum::CUSTOMER->value);
        }
    }

    private function sendMenu(): void
    {
        $this->user->sendMessage(
            new SendMessageCommand(
                params: new MessageParams(
                    message: trans('messages.registered')
                ),
                nextState: UserState::NEUTRAL
            )
        );

        $this->user->sendMessage(
            new SendMessageCommand(
                params: NeutralStateMessage::make()
            )
        );
    }
}
