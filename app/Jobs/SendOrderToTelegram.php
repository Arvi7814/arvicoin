<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendOrderToTelegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        $order = $this->order;

        $text = "🛒 Новый заказ #{$order->id}\n";
        $text .= "Имя: {$order->customer_name}\n";
        $text .= "Телефон: {$order->phone}\n";
        $text .= "Сумма: {$order->amount}\n";

        $token = env('NOTIFICATION_BOT_TOKEN');
        $chat_id = env('NOTIFICATION_CHAT_ID');

        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);
    }
}
