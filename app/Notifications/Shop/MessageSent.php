<?php

namespace App\Notifications\Shop;

use App\Models\Order\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\Resources\WebpushConfig;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use NotificationChannels\Fcm\Resources\WebpushFcmOptions;

class MessageSent extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Order $order
    ) {}

    public function via(): array
    {
        return [FcmChannel::class];
    }

    public function toFcm(): FcmMessage
    {
        $order = $this->order;
        $notification = FcmNotification::create()
            ->setTitle(trans('messages.new-order', [
                'client' => $order->user->first_name,
                'order' => $order->id
            ]));

        return FcmMessage::create()
            ->setNotification($notification)
            ->setWebpush(
                WebpushConfig::create()
                    ->setFcmOptions(
                        WebpushFcmOptions::create()
                            ->setLink(
                                route('filament.resources.order/orders.index')
                            )
                    )
            );
    }
}
