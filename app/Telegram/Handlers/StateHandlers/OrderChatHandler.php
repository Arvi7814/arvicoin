<?php
declare(strict_types=1);

namespace App\Telegram\Handlers\StateHandlers;

use App\Exceptions\TelegramException;
use App\Http\Services\Chat\MessageService;
use App\Telegram\Commands\MessageParams;
use App\Telegram\Commands\SendMessageCommand;
use App\Telegram\Contracts\StateHandler;
use InvalidArgumentException;

final class OrderChatHandler extends StateHandler
{
    public function handle(): void
    {
        try {
            if ($currentOrder = $this->user->currentOrder) {
                if($currentOrder->chat) {
                    $service = new MessageService();

                    $service->newMessageFromTelegram(
                        $this->request,
                        $currentOrder->chat,
                        $this->user
                    );
                } else {
                    $this->user->sendMessage(
                        new SendMessageCommand(
                            params: new MessageParams(
                                message: trans('messages.in-moderation')
                            ),
                            replaceLastMessage: true
                        )
                    );
                }
            } else {
                throw new InvalidArgumentException();
            }
        } catch (TelegramException|InvalidArgumentException $e) {
            $this->backToNeutral();
        }
    }
}
