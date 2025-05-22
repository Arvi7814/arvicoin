<?php

namespace App\Observers;

use App\Models\Order\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function created(Order $order): void
    {
        $token = env('NOTIFICATION_BOT_TOKEN');
        $chatId = env('NOTIFICATION_CHAT_ID');

        $message = "🆕 Новый заказ!\n"
                 . "Имя: {$order->name}\n"
                 . "Телефон: {$order->phone_number}\n"
                 . "Комментарий: {$order->comment}\n"
                 . "Источник: {$order->source}";

        try {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка отправки Telegram-сообщения: ' . $e->getMessage());
        }
    }
}
