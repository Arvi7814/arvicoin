<?php
declare(strict_types=1);

namespace App\Http\Services\Order;

use App\Enum\UserState;
use App\Models\Order\Order;
use App\Models\System\Setting;
use App\Telegram\Commands\MessageParams;
use App\Telegram\Commands\SendMessageCommand;
use App\Telegram\Messages\NeutralStateMessage;
use Illuminate\Support\Facades\DB;

final class OrderService
{
    public function deleteOrder(Order $order): void
    {
        $order->delete();
    }

    public function closeOrder(Order $order): void
    {
        if ($user = $order->user) {
            if ($user->current_order_id === $order->id) {
                DB::transaction(function () use ($user) {
                    $user->current_order_id = null;
                    $user->save();

                    app()->setLocale($user->language->value);

                    try {
                        $user->sendMessage(
                            new SendMessageCommand(
                                params: new MessageParams(
                                    message: Setting::tgOrderClosedMessage()->translation(),
                                    keyboard: NeutralStateMessage::keyboard()
                                ),
                                replaceLastMessage: false,
                                nextState: UserState::NEUTRAL
                            )
                        );
                    } catch (\Throwable $e) {
                        
                    }
                });
            }
        }
    }
}
