<?php

namespace App\Observers;

use App\Models\Order\Order;
use Illuminate\Support\Facades\Http;

class OrderObserver
{
    public function created(Order $order): void
    {
        $message = "🆕 Новый заказ!\n"
                 . "Имя: {$order->name}\n"
                 . "Телефон: {$order->phone_number}\n"
                 . "Комментарий: {$order->comment}\n"
                 . "Источник: {$order->source}";

        Http::post("https://api.telegram.org/bot7803226238:AAFmoqgPjqcF8PvT4ZoknkbQtJ6CnulE07U/sendMessage", [
            'chat_id' => '6323008199',
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);
    }
}
