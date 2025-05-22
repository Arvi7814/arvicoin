<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\LangEnum;
use App\Exceptions\TelegramException;
use App\Http\Requests\WebhookRequest;
use App\Integration\TgLogger;
use App\Models\User\User;
use App\Enum\UserState;
use App\Telegram\Handlers\CommandHandlers\StartCommandHandler;
use App\Telegram\Handlers\StateHandlers\ChangeLangHandler;
use App\Telegram\Handlers\StateHandlers\ChooseLangHandler;
use App\Telegram\Handlers\StateHandlers\EnterContactHandler;
use App\Telegram\Handlers\StateHandlers\InitialHandler;
use App\Telegram\Handlers\StateHandlers\NeutralHandler;
use App\Telegram\Handlers\StateHandlers\OrderChatHandler;
use App\Telegram\Handlers\StateHandlers\ProductSelectedHandler;
use App\Telegram\Handlers\StateHandlers\ShowSettingsHandler;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

final class TelegramController
{
    private TgLogger $logger;

    public function __construct()
    {
        $this->logger = new TgLogger();
    }

    public function __invoke(WebhookRequest $request): JsonResponse
    {
        if (!$request->respondable()) {
            return response()->json();
        }

        try {
            $user = $request->getUser();
            $this->setEnvironment($user);
            try {
                $this->handleCommand($request);
            } catch (TelegramException|InvalidArgumentException $e) {
                $this->handleState($request, $user->state);
            }
        } catch (\Throwable $e) {
            $this->logger->log($e);
        }

        return response()->json();
    }

    /**
     * @throws TelegramException|InvalidArgumentException
     */
    public function handleCommand(WebhookRequest $request): void
    {
        (match ($request->text()) {
            '/start', '/restart' => new StartCommandHandler($request),
            default => throw new InvalidArgumentException()
        })->handle();
    }

    public function handleState(WebhookRequest $request, string $state): void
    {
        (match ($state) {
            UserState::INITIAL => new InitialHandler($request),
            UserState::CHOOSE_LANG => new ChooseLangHandler($request),
            UserState::ENTER_CONTACT => new EnterContactHandler($request),
            UserState::NEUTRAL => new NeutralHandler($request),
            UserState::SHOW_SETTINGS => new ShowSettingsHandler($request),
            UserState::CHANGE_LANG => new ChangeLangHandler($request),
            UserState::PRODUCT_SELECTED => new ProductSelectedHandler($request),
            UserState::ORDERED => new OrderChatHandler($request)
        })->handle();
    }

    private function setEnvironment(User $user): void
    {
        app()->setLocale($user->language?->value ?? LangEnum::RU->value);
    }
}
