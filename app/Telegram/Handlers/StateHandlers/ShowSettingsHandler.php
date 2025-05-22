<?php
declare(strict_types=1);

namespace App\Telegram\Handlers\StateHandlers;

use App\Exceptions\TelegramException;
use App\Enum\UserState;
use App\Telegram\Commands\SendMessageCommand;
use App\Telegram\Contracts\StateHandler;
use App\Telegram\Messages\ChooseLangMessage;
use InvalidArgumentException;

final class ShowSettingsHandler extends StateHandler
{
    public function handle(): void
    {
        try {
            $text = $this->request->text();

            match ($text) {
                trans('messages.change-lang') => $this->changeLang(),
                default => throw new InvalidArgumentException()
            };

        } catch (TelegramException|InvalidArgumentException $e) {
            $this->backToNeutral();
        }
    }

    private function changeLang(): void
    {
        $this->user->sendMessage(
            new SendMessageCommand(
                params: ChooseLangMessage::make(),
                nextState: UserState::CHANGE_LANG
            )
        );
    }
}
