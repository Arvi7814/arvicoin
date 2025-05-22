<?php
declare(strict_types=1);

namespace App\Telegram\Handlers\StateHandlers;

use App\Exceptions\TelegramException;
use App\Enum\UserState;
use App\Telegram\Commands\SendMessageCommand;
use App\Telegram\Contracts\StateHandler;
use App\Telegram\Messages\ChooseLangMessage;
use App\Telegram\Messages\EnterContactMessage;
use InvalidArgumentException;

final class ChooseLangHandler extends StateHandler
{
    public function handle(): void
    {
        try {
            $text = $this->request->text();
            $langOptions = ChooseLangMessage::localeOptions();

            if (!array_key_exists($text, $langOptions)) {
                throw new InvalidArgumentException();
            }

            $this->user->language = $langOptions[$text];
            $this->user->update();
            app()->setLocale($this->user->language->value);

            $this->askContact();
        } catch (TelegramException|InvalidArgumentException $e) {
            $this->askCorrectData(
                ChooseLangMessage::make()
            );
        }
    }

    private function askContact(): void
    {
        $this->user->sendMessage(
            new SendMessageCommand(
                params: EnterContactMessage::make(),
                nextState: UserState::ENTER_CONTACT
            )
        );
    }
}
