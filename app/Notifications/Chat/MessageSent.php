<?php

namespace App\Notifications\Chat;

use App\Models\Chat\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\Resources\WebpushConfig;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use NotificationChannels\Fcm\Resources\WebpushFcmOptions;

class MessageSent extends Notification
{
    use Queueable;

    public function __construct(
        private readonly ChatMessage $chatMessage
    ) {}

    public function via(): array
    {
        return [FcmChannel::class];
    }

    public function toFcm(): FcmMessage
    {
        $chatMessage = $this->chatMessage;
        $notification = FcmNotification::create()
            ->setTitle(trans('messages.new-message', [
                'sender' => $chatMessage->user->first_name,
                'order' => $chatMessage->chat->order_id
            ]))
            ->setBody($chatMessage->content);

        return FcmMessage::create()
            ->setNotification($notification)
            ->setWebpush(
                WebpushConfig::create()
                    ->setFcmOptions(
                        WebpushFcmOptions::create()
                            ->setLink(
                                route('filament.resources.chat/chats.view', ['record' => $chatMessage->chat_id])
                            )
                    )
            )
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
