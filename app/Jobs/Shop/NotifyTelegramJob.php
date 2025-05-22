<?php

namespace App\Jobs\Shop;

use App\Enum\RoleEnum;
use App\Models\Chat\ChatMessage;
use App\Models\Order\Order;
use App\Models\User\User;
use App\Notifications\Shop\MessageSent;
use App\Telegram\Services\ChatService;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyTelegramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int         $userId,
        private readonly ChatMessage $chatMessage
    )
    {
    }

    public function handle()
    {
        if ($user = User::query()->whereNotNull(['chat_id', 'current_order_id'])->where('id', $this->userId)->first()) {
            app()->setLocale($user->language->value);
            $service = new ChatService();
            $service->sendMessage($user, $this->chatMessage);
        }
    }
}
