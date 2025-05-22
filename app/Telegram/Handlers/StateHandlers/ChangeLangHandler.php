<?php
declare(strict_types=1);

namespace App\Telegram\Handlers\StateHandlers;

use App\Exceptions\TelegramException;
use App\Telegram\Contracts\StateHandler;
use App\Telegram\Messages\ChooseLangMessage;
use InvalidArgumentException;

final class ChangeLangHandler extends StateHandler
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
        } catch (TelegramException|InvalidArgumentException $e) {

        }

        $this->backToNeutral();
    }
}
