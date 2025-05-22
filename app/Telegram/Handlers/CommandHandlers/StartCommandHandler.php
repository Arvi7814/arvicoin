<?php
declare(strict_types=1);

namespace App\Telegram\Handlers\CommandHandlers;

use App\Enum\UserState;
use App\Telegram\Commands\MessageParams;
use App\Telegram\Commands\SendMessageCommand;
use App\Telegram\Contracts\CommandHandler;
use App\Telegram\Messages\ChooseLangMessage;
use App\Telegram\Messages\NeutralStateMessage;

final class StartCommandHandler extends CommandHandler
{
    public function handle(): void
    {
        if ($this->user->current_order_id) {
            return;
        }
        
        if ($this->user->phone_number) {
            $this->restartUserState();
        } else {
            $this->greetUser();
        }
    }

    private function greetUser(): void
    {
        $command = new SendMessageCommand(
            params: new MessageParams(
                message: trans('messages.greeting'),
                keyboard: ChooseLangMessage::keyboard()
            ),
            nextState: UserState::CHOOSE_LANG
        );

        $this->user->sendMessage($command);
    }

    private function restartUserState()
    {
        $this->user->sendMessage(
            new SendMessageCommand(
                params: NeutralStateMessage::make(),
                nextState: UserState::NEUTRAL
            )
        );
    }
}
