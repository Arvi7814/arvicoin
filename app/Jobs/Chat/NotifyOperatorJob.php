<?php

namespace App\Jobs\Chat;

use App\Enum\ChatMessageTypeEnum;
use App\Models\Chat\ChatMessage;
use App\Notifications\Chat\MessageSent;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyOperatorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly ChatMessage $chatMessage
    )
    {
    }

    public function handle()
    {
        $chatMessage = $this->chatMessage;
        $notification = $this->getNotification($chatMessage);

        foreach ($chatMessage->chat->chatMembers as $chatMember) {
            if ($chatMember->user_id !== $chatMessage->user_id && $chatMember->user) {
                $notification->sendToDatabase($chatMember->user);

                event(new DatabaseNotificationsSent($chatMember->user));
                $chatMember->user->notify(
                    new MessageSent($chatMessage)
                );
            }
        }
    }

    private function getNotification(ChatMessage $chatMessage): Notification
    {
        $notification = Notification::make()
            ->success()
            ->title(trans('messages.new-message', [
                'sender' => $chatMessage->user->first_name,
                'order' => $chatMessage->chat->order_id
            ]))
            ->body($chatMessage->content);

        $actions = [];

        if ($chatMessage->type === ChatMessageTypeEnum::MEDIA) {
            $actions[] = (new Action('media'))
                ->label(trans('fields.file'))
                ->url($chatMessage->getFirstMediaUrl());
        }

        return $notification->actions(array_merge($actions, [
            (new Action('view'))
                ->label(trans('messages.to-chat'))
                ->url(route('filament.resources.chat/chats.view', [
                    'record' => $chatMessage->chat_id
                ]))
        ]));
    }
}
