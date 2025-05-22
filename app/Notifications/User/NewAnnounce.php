<?php

namespace App\Notifications\User;

use App\Models\Announce;
use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class NewAnnounce extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Announce $announce
    ) {}

    public function via(): array
    {
        return [FcmChannel::class];
    }

    public function toFcm(User $notifiable): FcmMessage
    {
        $announce = $this->announce;
        $notification = FcmNotification::create()
            ->setTitle($announce->translate('title', $notifiable->language->value))
            ->setBody($announce->translate('content', $notifiable->language->value));

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
