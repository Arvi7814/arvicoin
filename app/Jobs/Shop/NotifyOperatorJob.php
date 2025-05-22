<?php

namespace App\Jobs\Shop;

use App\Enum\RoleEnum;
use App\Models\Order\Order;
use App\Models\User\User;
use App\Notifications\Shop\MessageSent;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyOperatorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly Order $order
    )
    {
    }

    public function handle()
    {
        $notification = $this->getNotification();
        $users = User::query()
            ->whereHas(
                'roles',
                fn(Builder $query) => $query->whereIn('name', [
                    RoleEnum::MANAGER->value,
                    RoleEnum::MODERATOR->value,
                ])
            )
            ->get();
        foreach ($users as $user) {
            $notification->sendToDatabase($user);

            event(new DatabaseNotificationsSent($user));
            $user->notify(
                new MessageSent($this->order)
            );
        }
    }

    private function getNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(trans('messages.new-order', [
                'client' => $this->order->user->first_name,
                'order' => $this->order->id
            ]))
            ->actions([
                (new Action('view'))
                    ->label(trans('messages.to-chat'))
                    ->url(route('filament.resources.order/orders.index'))
            ]);
    }
}
