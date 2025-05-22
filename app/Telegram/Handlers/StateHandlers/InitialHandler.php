<?php
declare(strict_types=1);

namespace App\Telegram\Handlers\StateHandlers;

use App\Enum\UserState;
use App\Telegram\Commands\MessageParams;
use App\Telegram\Commands\SendMessageCommand;
use App\Telegram\Contracts\StateHandler;
use App\Telegram\Messages\ChooseLangMessage;

final class InitialHandler extends StateHandler
{
    public function handle(): void
    {
        $this->greetUser();
    }

    private function greetUser(): void
    {
        $this->user->sendMessage(
            new SendMessageCommand(
                params: new MessageParams(
                    message: trans('messages.greeting'),
                    keyboard: ChooseLangMessage::keyboard()
                ),
                nextState: UserState::CHOOSE_LANG
            )
        );
    }
}
