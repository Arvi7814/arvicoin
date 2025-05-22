<?php
declare(strict_types=1);

namespace App\Telegram\Contracts;

use App\Http\Requests\WebhookRequest;
use App\Models\Shop\Cart;
use App\Models\User\User;
use App\Enum\UserState;
use App\Telegram\Commands\MessageParams;
use App\Telegram\Commands\SendMessageCommand;
use App\Telegram\Messages\NeutralStateMessage;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

abstract class Handler
{
    protected User $user;

    public function __construct(
        protected readonly WebhookRequest $request
    )
    {
        $this->user = $this->request->getUser();
    }

    public abstract function handle(): void;

    public function deleteCurrentMessage(): void
    {
        try {
            Telegram::bot()->deleteMessage([
                'chat_id' => $this->request->chatId(),
                'message_id' => $this->request->messageId()
            ]);
        } catch (Throwable $e) {

        }
    }

    public function askCorrectValue(string $text): void
    {
        $this->user->sendMessage(
            new SendMessageCommand(
                params: new MessageParams(
                    message: join(PHP_EOL, [
                        $text,
                        PHP_EOL,
                        "<b>" . trans('messages.enter-correct-value') . "</b>"
                    ])
                )
            )
        );
    }

    public function askCorrectData(MessageParams $params): void
    {
        $this->user->sendMessage(
            new SendMessageCommand(
                params: new MessageParams(
                    message: join(PHP_EOL, [
                        $params->message,
                        PHP_EOL,
                        "<b>" . trans('messages.select-correct-value') . "</b>"
                    ]),
                    inlineKeyboard: $params->inlineKeyboard,
                    keyboard: $params->keyboard,
                    requestContact: $params->requestContact
                )
            )
        );
    }

    public function backToNeutral(): void
    {
        $this->user->sendMessage(
            new SendMessageCommand(
                params: NeutralStateMessage::make(),
                nextState: UserState::NEUTRAL
            )
        );
    }
}
