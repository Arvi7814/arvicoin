<?php
declare(strict_types=1);

namespace App\Telegram\Handlers\StateHandlers;

use App\Enum\UserState;
use App\Models\Shop\Product;
use App\Models\System\Setting;
use App\Telegram\Commands\MessageParams;
use App\Telegram\Commands\SendMediaCommand;
use App\Telegram\Commands\SendMessageCommand;
use App\Telegram\Contracts\StateHandler;
use App\Telegram\Messages\NeutralStateMessage;
use App\Telegram\Messages\ProductMessage;
use App\Telegram\Messages\SettingsMessage;
use App\Telegram\Services\ProductService;
use InvalidArgumentException;

final class NeutralHandler extends StateHandler
{
    public function handle(): void
    {
        try {
            $this->user->clearCart();
            $text = $this->request->text();

            match ($text) {
                trans('messages.about-us') => $this->showInfo(),
                trans('messages.settings') => $this->showSettings(),
                default => $this->showProductInfo($text)
            };
        } catch (InvalidArgumentException $e) {
            $this->backToNeutral();
        }
    }

    private function showInfo(): void
    {
        $this->user->sendMessage(
            new SendMessageCommand(
                params: new MessageParams(
                    message: Setting::tgInfo()->translation(),
                    keyboard: NeutralStateMessage::keyboard()
                )
            )
        );
    }

    private function showSettings(): void
    {
        $this->user->sendMessage(
            new SendMessageCommand(
                params: SettingsMessage::make(),
                nextState: UserState::SHOW_SETTINGS
            )
        );
    }

    private function showProductInfo(string $text)
    {
        $product = Product::query()
            ->search($text)
            ->first();

        if ($product) {
            $cartProduct = $this->user->getCartProduct($product);

            $service = new ProductService();
            $service->sendInfo($this->user, $cartProduct);
        } else {
            $this->backToNeutral();
        }
    }
}
