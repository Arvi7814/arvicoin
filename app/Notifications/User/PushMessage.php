<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class PushMessage extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $title,
        private readonly string $body
    ) {}

    public function via(): array
    {
        return [FcmChannel::class];
    }

    public function toFcm(): FcmMessage
    {
        $notification = FcmNotification::create()
            ->setTitle($this->title)
            ->setBody($this->body);

        return FcmMessage::create()
            ->setNotification($notification)
            ->setAndroid(
                AndroidConfig::create()
                    ->setNotification(
                        AndroidNotification::create()
                            ->setTitle($notification->getTitle())
                            ->setBody($notification->getBody())
                            ->setSound('default')
                    )
            );
    }
}
